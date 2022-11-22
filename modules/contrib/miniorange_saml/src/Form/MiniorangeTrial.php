<?php
namespace Drupal\miniorange_saml\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\miniorange_saml\MiniorangeSAMLConstants;
use Drupal\miniorange_saml\Utilities;

class MiniorangeTrial extends MiniorangeSAMLFormBase
{

  /**
   * @return string
   */
    public function getFormId()
    {
        return 'miniorange_saml_trial';
    }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $email  = $this->config->get('miniorange_saml_customer_admin_email');

        $form['miniorange_saml_demo'] = array(
        '#type'      => 'container',
        '#prefix'   => '<div id="modal_support_form">',
        '#suffix'   => '</div>',
        );
        $form['miniorange_saml_demo']['mo_otp_verification_script'] = array(
        '#attached' => array('library' => array('core/drupal.dialog.ajax','miniorange_saml/miniorange_saml.admin')),
        );
        $form['miniorange_saml_demo']['mo_otp_verification_status_messages'] = array(
        '#type'     => 'status_messages',
        '#weight'   => -10,
        );

        $form['miniorange_saml_demo']['mo_saml_demo_email_address'] = array(
        '#type' => 'email',
        '#title' => t('Email'),
        '#default_value' => $email,
        '#required' => true,
        '#attributes' => array('placeholder' => t('Enter your email'), 'style' => 'width:99%;margin-bottom:1%;'),
        );

        $form['miniorange_saml_demo']['mo_saml_demo_plan'] = array(
        '#type' => 'select',
        '#title' => t('Plan'),
        '#options' => array(
          'Drupal 8 SAML SP Standard' => t('Drupal 8 SAML SP Standard'),
          'Drupal 8 SAML SP Premium' => t('Drupal 8 SAML SP Premium'),
          'Drupal 8 SAML SP Enterprise' => t('Drupal 8 SAML SP Enterprise'),
          'Drupal 8 SAML SP + Website Security Premium' => t('Drupal 8 SAML SP + Website Security Premium'),
          'Not Sure' => t('Not Sure, Need help for selecting plan.'),
        ),
        '#attributes' => array('style' => 'width:99%;height:30px;margin-bottom:1%;'),
        '#description' => $this->t('If you are not sure with which plan you should go with,
                   get in touch with us on <a href="mailto:'.MiniorangeSAMLConstants::SUPPORT_EMAIL.'">'
          .MiniorangeSAMLConstants::SUPPORT_EMAIL.'</a>
                   and we will assist you with the suitable plan.</div>'),
        );

        $form['miniorange_saml_demo']['mo_saml_demo_description'] = array(
        '#type' => 'textarea',
        '#title' => t('Description'),
        '#required' => true,
        '#attributes' => array('placeholder' => t('Describe your use case here!'), 'style' => 'width:99%;'),
        '#suffix' => '<br>',
        );
        $form['miniorange_saml_demo']['actions'] = array(
        '#type' => 'actions',
        );

        $form['miniorange_saml_demo']['actions']['submit'] = array(
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Send Request'),
        '#attributes' => array('class' => array('use-ajax')),
        '#ajax' => array(
          'callback' => '::submitTrialRequest',
          'progress'  => array(
            'type'    => 'throbber',
            'message' => $this->t('Sending Request...'),
          ),
        ),
        );
        
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitTrialRequest(array &$form, FormStateInterface $form_state)
    {
        $response = new AjaxResponse();
        if ($form_state->hasAnyErrors()) {
            $response->addCommand(new ReplaceCommand('#modal_support_form', $form));
        } else {
            $form_values = $form_state->getValues();
            $email = $form_values['mo_saml_demo_email_address'];
            $phone = $form_values['mo_saml_demo_plan'];
            $query = $form_values['mo_saml_demo_description'];
            $query_type = 'Demo Request';

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
