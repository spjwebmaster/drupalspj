<?php

namespace Drupal\config_batch_export\Controller;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\config\Controller\ConfigController;
use Drupal\Core\Archiver\ArchiveTar;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\system\FileDownloadController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;

/**
 * Returns responses for config_batch_export module routes.
 */
class ConfigBatchExportController extends ConfigController {

  use StringTranslationTrait;
  use MessengerTrait;

  /**
   * How many elements of the array to process per one operation.
   */
  const BATCH_SIZE = 10;

  const LOCK_ID = 'config_batch_export_download';

  /**
   * File system.
   *
   * @return \Drupal\Core\File\FileSystemInterface
   *   File system.
   */
  protected static function getFilesystem() {
    return \Drupal::service('file_system');
  }

  /**
   * Batch callback.
   */
  public static function callbackBatchJob($batch_data, &$context) {
    $sandbox = &$context['sandbox'];
    if (!isset($sandbox['started'])) {
      /** @var \Drupal\file\FileInterface $file */
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($batch_data['file_id']);

      $sandbox['filename'] = $file->getFileUri();
      $sandbox['started'] = TRUE;
      $sandbox['offset'] = 0;
      $sandbox['total'] = count($batch_data['configs']);
      $sandbox['batch_size'] = static::BATCH_SIZE;
      $context['results']['file_id'] = $batch_data['file_id'];
    }

    $configs = array_slice($batch_data['configs'], $sandbox['offset'], $sandbox['batch_size']);
    $collection_storage_cache = [];
    if (!empty($configs)) {
      // Create uncompressed archive.
      $archiver = new ArchiveTar($sandbox['filename']);

      $config_factory = \Drupal::configFactory();
      /** @var \Drupal\Core\Config\StorageInterface $target_storage */
      $target_storage = \Drupal::service('config.storage');
      foreach ($configs as $config_definition) {
        if (is_array($config_definition)) {
          // Process collection config.
          $name = $config_definition['name'];
          $collection = $config_definition['collection'];
          $collection_storage = $collection_storage_cache[$collection] ?? ($collection_storage_cache[$collection] = $target_storage->createCollection($collection));
          $archiver->addString(str_replace('.', '/', $collection) . "/$name.yml", Yaml::encode($collection_storage->read($name)));
        }
        else {
          $name = $config_definition;
          $archiver->addString("$name.yml", Yaml::encode($config_factory->get($name)->getRawData()));
        }
      }

      $context['finished'] = $sandbox['offset'] / $sandbox['total'];

      // Call destructor.
      unset($archiver);
    }
    else {
      $context['finished'] = 1;
    }

    $sandbox['offset'] += $sandbox['batch_size'];

    $context['message'] = t('Processed @current out of @total', [
      '@current' => $context['sandbox']['offset'],
      '@total' => $context['sandbox']['total'],
    ]);
  }

  /**
   * Batch finished callback.
   */
  public static function callbackBatchFinished($status, $results) {
    /** @var \Drupal\file\FileInterface $file */
    $file = \Drupal::entityTypeManager()->getStorage('file')->load($results['file_id']);
    $oldpath = $file->getFileUri();

    $file->setFilename('configs.tar');
    $file->setFileUri('private://' . $file->getFilename());

    if (extension_loaded('zlib')) {
      $file->setFilename($file->getFilename() . '.gz');
      $file->setFileUri('private://' . $file->getFilename());

      $gzfp = gzopen($file->getFileUri(), 'wb');
      $tarfp = fopen($oldpath, 'rb');
      while (!feof($tarfp) && FALSE !== ($buff = fread($tarfp, 4096))) {
        gzwrite($gzfp, $buff);
      }

      gzclose($gzfp);
      fclose($tarfp);
    }
    else {
      $destination =  static::getFilesystem()->move($oldpath, 'private://', FileSystemInterface::EXISTS_REPLACE);

      @rename($destination, $file->getFileUri());
    }

    // Ensure the temp file is deleted.
    static::getFilesystem()->delete($oldpath);

    $file->save();

    $url = Url::fromRoute('config_batch_export.export_download_file', [
      'file' => $file->id(),
    ]);

    \Drupal::messenger()->addMessage(t('Config file created at @created can be downloaded here <a href="@url">here</a>', [
      '@url' => $url->toString(),
      '@created' => date(DateTimeItemInterface::DATE_STORAGE_FORMAT . ' H:i:s'),
    ]));

    static::unlock();
  }

  /**
   * Downloads a tarball of the site configuration.
   */
  public function downloadExport() {
    $redirect_url = Url::fromRoute('config.export_full')->toString();
    if (!static::lock()) {
      $this->messenger()->addError($this->t("Can't lock the operation"));
      return new RedirectResponse($redirect_url);
    }

    $file_system = static::getFilesystem();
    $filepath = $file_system->tempnam('temporary://', hash('adler32', 'config_batch_export'));

    // @todo: Use dependency injection for all \Drupal calls.
    // @todo: Use different temporary files with random names.
    // @todo: Control gurbage collection.
    $existing_files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => 'private://configs.tar.gz']);
    $existing_files += \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => 'private://configs.tar']);
    if (!empty($existing_files)) {
      /** @var \Drupal\file\FileInterface $file */
      foreach ($existing_files as $file) {
        try {
          $file->delete();
        }
        catch (EntityStorageException $e) {
          watchdog_exception('config_batch_export.routing', $e);
        }

        if (file_exists($file->getFileUri())) {
          $file_system->delete($file->getFileUri());
        }
      }
    }

    // Ensure the file is deleted.
    if (file_exists($filepath)) {
      $file_system->delete($filepath);
    }

    $file = File::create([
      'uri' => $filepath,
      'uid' => \Drupal::currentUser()->id(),
    ]);

    $file->setTemporary();
    try {
      $file->save();
    }
    catch (EntityStorageException $e) {
      watchdog_exception('config_batch_export.routing', $e);
    }


    $batch_data = [
      'file_id' => $file->id(),
      'configs' => $this->configManager->getConfigFactory()->listAll(),
    ];

    // Get all override data from the remaining collections.
    foreach ($this->targetStorage->getAllCollectionNames() as $collection) {
      $collection_storage = $this->targetStorage->createCollection($collection);
      foreach ($collection_storage->listAll() as $name) {
        $batch_data['configs'][] = [
          'name' => $name,
          'collection' => $collection,
        ];
      }
    }

    $batch = [
      'title' => $this->t('Generation of a configs archive'),
      'operations' => [
        [
          [static::class, 'callbackBatchJob'],
          [$batch_data],
        ],
      ],
      'finished' => [static::class, 'callbackBatchFinished'],
    ];

    batch_set($batch);

    return batch_process($redirect_url);
  }

  /**
   * Tries to lock the operation.
   *
   * @param bool $force
   *
   * @return bool
   */
  public static function lock($force = FALSE) {
    if ($force) {
      static::unlock(TRUE);
    }

    return static::lockBackend()->acquire(static::LOCK_ID, 600);
  }

  /**
   * Unlocks the operation.
   *
   * @param boolean $force
   *   If true, will ensure the lock is removed.
   */
  public static function unlock($force = FALSE) {
    // @todo: In case if $force = TRUE ensure the lock is deleted.
    static::lockBackend()->release(static::LOCK_ID);
  }

  /**
   * Get lock backend.
   *
   * @return \Drupal\Core\Lock\LockBackendInterface
   */
  protected static function lockBackend() {
    return \Drupal::service('lock.persistent');
  }

  /**
   * Checks lock status.
   *
   * @return bool
   *   TRUE, if locked. FALSE - if unlocked.
   */
  public static function isLocked() {
    $is_unlocked = static::lockBackend()->lockMayBeAvailable(static::LOCK_ID);
    return !$is_unlocked;
  }

  /**
   * Downloads a file.
   *
   * @param \Drupal\file\FileInterface $file
   *   File object.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   Response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function downloadExportFile(FileInterface $file) {
    if (static::isLocked()) {
      throw new AccessDeniedHttpException();
    }

    $request = new Request([
      'file' => basename($file->getFileUri()),
    ]);

    // Make the file old to delete it in file_cron.
    $file
      ->setChangedTime(1)
      ->save();

    $scheme = StreamWrapperManager::getScheme($file->getFileUri());
    $download_controller = new FileDownloadController(\Drupal::service('stream_wrapper_manager'));
    $response = $download_controller->download($request, $scheme);
    return $response;
  }

}
