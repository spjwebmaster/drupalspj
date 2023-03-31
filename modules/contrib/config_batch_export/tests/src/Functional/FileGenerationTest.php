<?php

namespace Drupal\Tests\config_batch_export\Functional;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Core\Archiver\ArchiveTar;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Config batch export test.
 *
 * @package Drupal\config_batch_export\Tests
 * @group config_batch_export
 */
class FileGenerationTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['config_batch_export'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  protected $extension;

  protected $filepath;

  protected $headers;

  protected $content;

  protected $fpdir;

  /**
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalLogin($this->drupalCreateUser([
      'export configuration',
    ]));

    $this->assertTrue(TRUE);
    $this->fileSystem = $this->container->get('file_system');
  }

  /**
   *  Test batch processing.
   */
  public function testBatchProcessing() {
    $this->drupalPostForm('admin/config/development/configuration/full/export', [], 'edit-export-batch');
    $this->clickLink('here');

    $this->headers = $this->getSession()->getResponseHeaders();
    $this->content = $this->getSession()->getPage()->getContent();

    $this->assertTrue(isset($this->headers['Content-disposition']) && isset($this->headers['Content-disposition'][0]), 'File download header exists');

    $this->_testFileExtension();
    $this->_testFilesize();
    $this->_testArchive();
  }

  public function _testFileExtension() {
    $re = '/attachment;\sfilename\=\"(.*)\"/i';
    $this->assertNotFalse(preg_match($re, $this->headers['Content-disposition'][0], $reg), 'Filename exists');
    if ($this->hasFailed()) {
      return;
    }

    $filename = $reg[1];
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $this->assertTrue(in_array($extension, ['tar', 'gz']), 'File is archive');
    $this->extension = $extension;
  }

  public function _testFilesize() {
    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = $this->fileSystem;
    $this->filepath = $file_system->tempnam('temporary://', hash('adler32', 'config_batch_export')) . '.' . $this->extension;
    $file_content = $this->content;
    file_put_contents($this->filepath, $file_content);
    unset($file_content);

    $this->assertTrue(filesize($this->filepath) > 0, 'Filesize is more than 0');
  }

  public function _testArchive() {
    $extension = $this->extension;
    if ($extension == 'gz') {
      $gzfp = gzopen($this->filepath, 'rb');
      $tarfile = $this->fileSystem->tempnam('temporary://', hash('adler32', 'config_batch_export')) . '.tar';
      $tarfp = fopen($tarfile, 'wb');
      while (!feof($gzfp) && FALSE !== ($buff = gzread($gzfp, 4096))) {
        fwrite($tarfp, $buff);
      }

      gzclose($gzfp);
      fclose($tarfp);

      $this->fileSystem->delete($this->filepath);
      $this->filepath = $tarfile;
    }

    $tar = new ArchiveTar($this->filepath);
    $this->assertTrue(count($tar->listContent()) > 0, 'Archive has readable content');

    $tempdir = $this->fileSystem->tempnam('temporary://', hash('adler32', 'config_batch_export'));
    $this->fileSystem->delete($tempdir);
    $this->fileSystem->mkdir($tempdir, 0755, TRUE);
    $this->assertTrue(file_exists($tempdir));
    $this->assertTrue($tar->extract($tempdir));

    $this->fpdir = opendir($tempdir);
    while (($filename = readdir($this->fpdir))) {
      if ($filename == '.' || $filename == '..') {
        continue;
      }

      try {
        $filepath = $tempdir . '/' . $filename;
        $file_content = file_get_contents($filepath);
        $this->fileSystem->delete($filepath);

        $decoded = Yaml::decode($file_content);
      }
      catch (\Drupal\Component\Serialization\Exception\InvalidDataTypeException $e) {
        $this->fail('Yaml data incorrect');
      }

      $this->assertTrue(is_array($decoded) || is_object($decoded));
    }
  }

  public function tearDown() {
    $this->fileSystem->delete($this->filepath);

    if ($this->fpdir) {
      closedir($this->fpdir);
    }

    parent::tearDown();
  }

}
