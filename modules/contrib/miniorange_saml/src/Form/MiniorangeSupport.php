<?php
/**
 * @file
 * Contains support form for miniOrange 2FA Login Module.
 */
namespace Drupal\miniorange_saml\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\miniorange_saml\MiniorangeSAMLConstants;
use Drupal\miniorange_saml\Utilities;

/**
 *  Showing Support form info.
 */
class MiniorangeSupport extends MiniorangeSAMLFormBase
{
    public function getFormId()
    {
        return 'miniorange_SAML_support';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $email  = $this->config->get('miniorange_saml_customer_admin_email');
        $phone  = $this->config->get('miniorange_saml_customer_admin_phone');

        $form['miniorange_saml_support'] = array(
        '#type'      => 'container',
        '#prefix'   => '<div id="modal_support_form">',
        '#suffix'   => '</div>',
        );
        $form['miniorange_saml_support']['mo_otp_verification_script'] = array(
        '#attached' => array('library' => array('core/drupal.dialog.ajax','miniorange_saml/miniorange_saml.admin')),
        );
        $form['miniorange_saml_support']['mo_otp_verification_status_messages'] = array(
        '#type'     => 'status_messages',
        '#weight'   => -10,
        );

        $form['miniorange_saml_support']['mo_saml_markup_1'] = array(
        '#markup' => t('<p class="mo_saml_highlight_background_note">
                   Need any help? We can help you with configuring miniOrange SAML SP module on your site.
                   Just send us a query and we will get back to you soon.</p>'),
        );

        $form['miniorange_saml_support']['mo_saml_support_email_address'] = array(
        '#type'          => 'email',
        '#title'         => $this->t('Email'),
        '#default_value' => $email,
        '#required'      => true,
        '#attributes'    => array('placeholder' => t('Enter your email'), 'style' => 'width:99%;margin-bottom:1%;'),
        );
        $form['miniorange_saml_support']['mo_saml_support_phone_number'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Phone'),
        '#default_value' => $phone,
        '#attributes'    => array('placeholder' => $this->t('Enter number with country code Eg. +00xxxxxxxxxx'),
          'style' => 'width:99%;margin-bottom:1%;'),
        );
        $form['miniorange_saml_support']['mo_saml_support_query'] = array(
        '#type'          => 'textarea',
        '#title'         => $this->t('Query'),
        '#required'      => true,
        '#attributes'    => array('placeholder' => $this->t('Describe your query here!'), 'style' => 'width:99%'),
        '#suffix'        => '<br>',
        );

        $form['miniorange_saml_support']['actions'] = array(
        '#type' => 'actions',
        );

        $form['miniorange_saml_support']['actions']['submit'] = array(
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Submit query'),
        '#attributes' => array('class' => array('use-ajax')),
        '#ajax' => array(
          'callback' => '::submitQuery',
          'progress'  => array(
            'type'    => 'throbber',
            'message' => $this->t('Sending Query...'),
          ),
        ),
        );
        $form['miniorange_saml_support']['markup_support_note'] = array(
        '#markup' => $this->t('<div>If you want custom features in the module,
                          just drop an email to <a href="mailto:info@xecurify.com">info@xecurify.com</a> or
                          <a href="mailto:'.MiniorangeSAMLConstants::SUPPORT_EMAIL.'">'.
          MiniorangeSAMLConstants::SUPPORT_EMAIL.'</a></div>'),
        );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitQuery(array &$form, FormStateInterface $form_state)
    {
        $response = new AjaxResponse();
        if ($form_state->hasAnyErrors()) {
            $response->addCommand(new ReplaceCommand('#modal_support_form', $form));
        } else {
            $form_values = $form_state->getValues();
            $email = $form_values['mo_saml_support_email_address'];
            $phone = $form_values['mo_saml_support_phone_number'];
            $query = $form_values['mo_saml_support_query'];
            $query_type = 'Support';

            $support_response = Utilities::send_support_query($email, $phone, $query, $query_type);
            if ($support_response) {
                $message = array(
                '#type' => 'item',
                '#markup' => $this->t('Thanks for getting in touch! We will get back to you shortly.'),
                );
                $ajax_form = new OpenModalDialogCommand('Thank you!', $message, ['width' => '50%']);
            } else {
                $error = array(
                '#type' => 'item',
                '#markup' => $this->t('Error submitting the support query. Please send us your query at
                             <a href="mailto:drupalsupport@xecurify.com">
                             drupalsupport@xecurify.com</a>.'),
                );
                $ajax_form = new OpenModalDialogCommand('Error!', $error, ['width' => '50%']);
            }
            $response->addCommand($ajax_form);
        }
        return $response;
    }
}
