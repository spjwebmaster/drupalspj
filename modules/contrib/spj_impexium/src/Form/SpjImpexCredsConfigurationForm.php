<?php

namespace Drupal\spj_impexium\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form definition for the salutation message.
 */
class SpjImpexCredsConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['spj_impexium.creds'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spj_impexium_creds_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('spj_impexium.creds');

    $form['ACCESS_END_POINT'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ACCESS_END_POINT'),
      '#description' => $this->t('Please provide the ACCESS_END_POINT you want to use.'),
      '#default_value' => $config->get('ACCESS_END_POINT'),
    ];

    $form['APP_NAME'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APP_NAME'),
      '#description' => $this->t('Please provide the APP_NAME of your Impexium site'),
      '#default_value' => $config->get('APP_NAME'),
    ];

    $form['APP_KEY'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APP_KEY'),
      '#description' => $this->t('Please provide the APP_KEY of your Impexium site'),
      '#default_value' => $config->get('APP_KEY'),
    ];

    $form['APP_ID'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APP_ID'),
      '#description' => $this->t('Please provide the APP_ID of your Impexium site'),
      '#default_value' => $config->get('APP_ID'),
    ];
    
    $form['APP_PASSWORD'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APP_PASSWORD'),
      '#description' => $this->t('Please provide the APP_PASSWORD of your Impexium site'),
      '#default_value' => $config->get('APP_PASSWORD'),
    ];

    $form['APP_USER_EMAIL'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APP_USER_EMAIL'),
      '#description' => $this->t('Please provide the APP_USER_EMAIL of your Impexium site'),
      '#default_value' => $config->get('APP_USER_EMAIL'),
    ];

    $form['APP_USER_PASSWORD'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APP_USER_PASSWORD'),
      '#description' => $this->t('Please provide the APP_USER_PASSWORD of your Impexium site'),
      '#default_value' => $config->get('APP_USER_PASSWORD'),
    ];

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state){
      $salutation = $form_state->getValue('ACCESS_END_POINT');
      if(strlen($salutation) > 255){
          $form_state->setErrorByName('ACCESS_END_POINT',$this->t('This ACCESS_END_POINT is too long'));
      }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('spj_impexium.creds')
      ->set('ACCESS_END_POINT', $form_state->getValue('ACCESS_END_POINT'))
      ->set('APP_NAME', $form_state->getValue('APP_NAME'))
      ->set('APP_KEY', $form_state->getValue('APP_KEY'))
      ->set('APP_ID', $form_state->getValue('APP_ID'))
      ->set('APP_PASSWORD', $form_state->getValue('APP_PASSWORD'))
      ->set('APP_USER_EMAIL', $form_state->getValue('APP_USER_EMAIL'))
      ->set('APP_USER_PASSWORD', $form_state->getValue('APP_USER_PASSWORD'))
      ->save();

    parent::submitForm($form, $form_state);
    \Drupal::messenger()->addMessage("Submitted!");
  }

}