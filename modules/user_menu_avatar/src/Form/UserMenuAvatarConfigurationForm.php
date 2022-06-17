<?php

namespace Drupal\user_menu_avatar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines our form class.
 */
class UserMenuAvatarConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_menu_avatar_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'user_menu_avatar.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('user_menu_avatar.settings');

    $avatar_shape_options = [
      'circle' => $this->t('Circle'),
      'square' => $this->t('Square'),
    ];

    $avatar_yes_no_options = [
      'yes' => $this->t('Yes'),
      'no' => $this->t('No'),
    ];

    $form['user_avatar_heading'] = [
      '#type' => 'item',
      '#markup' => $this->t('<h2>Available User Menu Avatar Settings</h2>'),
      '#weight' => -10,
    ];

    $form['link_settings_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Link Settings'),
      '#attributes' => [
        'class' => [
          'link-settings-wrapper',
        ],
      ],
    ];

    $form['link_settings_wrapper']['link_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set link Text'),
      '#required' => TRUE,
      '#description' => $this->t('Set the text of the menu link to be replaced.'),
      '#default_value' => $config->get('link_text') ?: '',
    ];

    $form['logged_in_user_wraper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Logged-in User Settings'),
      '#attributes' => [
        'class' => [
          'logged-in-user-wrapper',
        ],
      ],
    ];

    $form['logged_in_user_wraper']['show_menu_avatar'] = [
      '#type' => 'radios',
      '#title' => $this->t('Show Avatar'),
      '#required' => TRUE,
      '#options' => $avatar_yes_no_options,
      '#description' => $this->t('Choose to show the user avatar.'),
      '#default_value' => $config->get('show_menu_avatar') ?: 'no',
    ];

    $form['logged_in_user_wraper']['avatar_shape'] = [
      '#type' => 'radios',
      '#title' => $this->t('User Menu Avatar Shape'),
      '#required' => TRUE,
      '#options' => $avatar_shape_options,
      '#description' => $this->t('Choose the shape of the avatar.'),
      '#default_value' => $config->get('avatar_shape') ?: 'circle',
      '#states' => [
        'visible' => [
          ':input[name="show_menu_avatar"]' => ['value' => 'yes'],
        ],
      ],
    ];

    $form['logged_in_user_wraper']['avatar_size'] = [
      '#type' => 'number',
      '#title' => $this->t('User Menu Avatar Size (px)'),
      '#field_suffix' => 'px',
      '#required' => TRUE,
      '#description' => $this->t('Set the size of the avatar in "pixels". Applies to both width and height. Numeric value only.'),
      '#maxlength' => 4,
      '#size' => 4,
      '#default_value' => $config->get('avatar_size') ?: '50',
      '#states' => [
        'visible' => [
          ':input[name="show_menu_avatar"]' => ['value' => 'yes'],
        ],
      ],
    ];

    $form['logged_in_user_wraper']['avatar_image_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Image field name'),
      '#required' => TRUE,
      '#description' => $this->t('Set the field name to use for avatar. Default is "user_picture".'),
      '#maxlength' => 140,
      '#size' => 60,
      '#default_value' => $config->get('avatar_image_field') ?: 'user_picture',
      '#states' => [
        'visible' => [
          ':input[name="show_menu_avatar"]' => ['value' => 'yes'],
        ],
      ],
    ];

    $form['logged_in_user_wraper']['show_user_name'] = [
      '#type' => 'radios',
      '#title' => $this->t('Show User Name'),
      '#required' => TRUE,
      '#options' => $avatar_yes_no_options,
      '#description' => $this->t('Choose to show the user name. Defaults to "displayName" value.'),
      '#default_value' => $config->get('show_user_name') ?: 'no',
    ];

    $form['logged_in_user_wraper']['avatar_custom_name_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom name field name'),
      '#required' => FALSE,
      '#description' => $this->t('Use a custom field for the user menu name. Leave blank to use default "displayName" value.'),
      '#maxlength' => 140,
      '#size' => 60,
      '#default_value' => $config->get('avatar_custom_name_field') ?: '',
      '#states' => [
        'visible' => [
          ':input[name="show_user_name"]' => ['value' => 'yes'],
        ],
      ],
    ];

    $form['anonymous_user_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Anonymous User Settings'),
      '#attributes' => [
        'class' => [
          'anonymous-user-wrapper',
        ],
      ],
    ];

    $form['anonymous_user_wrapper']['show_anonymous_avatar'] = [
      '#type' => 'radios',
      '#title' => $this->t('Show Anonymous Avatar'),
      '#required' => TRUE,
      '#options' => $avatar_yes_no_options,
      '#description' => $this->t('Choose to show the anonoymous user avatar.'),
      '#default_value' => $config->get('show_anonymous_avatar') ?: 'no',
    ];

    $form['anonymous_user_wrapper']['anonymous_user_avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Anonymous User Avatar'),
      '#required' => FALSE,
      '#description' => $this->t('Set an avatar for anonymous users.'),
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_validators' => [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => [25600000],
      ],
      '#upload_location' => 'public://user-menu-avatar/anonymous-avatar',
      '#default_value' => $config->get('anonymous_user_avatar') ?: NULL,
    ];

    $form['anonymous_user_wrapper']['show_anonymous_name'] = [
      '#type' => 'radios',
      '#title' => $this->t('Show Anonymous Name'),
      '#required' => TRUE,
      '#options' => $avatar_yes_no_options,
      '#description' => $this->t('Choose to show the anonoymous user name.'),
      '#default_value' => $config->get('show_anonymous_name') ?: 'no',
    ];

    $form['anonymous_user_wrapper']['custom_anonymous_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom Anonymous Text'),
      '#required' => FALSE,
      '#description' => $this->t('Set custom text to show for anonymous users. Leave blank to show "Anonymous".'),
      '#maxlength' => 255,
      '#size' => 60,
      '#default_value' => $config->get('custom_anonymous_text') ?: '',
      '#states' => [
        'visible' => [
          ':input[name="show_anonymous_name"]' => ['value' => 'yes'],
        ],
      ],
    ];

    $form['form_info'] = [
      '#type' => 'item',
      '#weight' => 10,
      '#markup' => $this->t('<p>User Menu Avatar uses Background-image CSS to position the user picture. The "width" and "height" are set by inline styles on the span element. The "border-radius" only applies if you choose shape circle.</p>'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $this->configFactory->getEditable('user_menu_avatar.settings')
      ->set('avatar_shape', $values['avatar_shape'])
      ->set('link_text', $values['link_text'])
      ->set('avatar_size', $values['avatar_size'])
      ->set('avatar_image_field', $values['avatar_image_field'])
      ->set('show_menu_avatar', $values['show_menu_avatar'])
      ->set('show_user_name', $values['show_user_name'])
      ->set('avatar_custom_name_field', $values['avatar_custom_name_field'])
      ->set('show_anonymous_avatar', $values['show_anonymous_avatar'])
      ->set('anonymous_user_avatar', $values['anonymous_user_avatar'])
      ->set('show_anonymous_name', $values['show_anonymous_name'])
      ->set('custom_anonymous_text', $values['custom_anonymous_text'])
      ->save();

    parent::submitForm($form, $form_state);

    // Flush render cache.
    \Drupal::service('cache.render')->invalidateAll();

  }

}
