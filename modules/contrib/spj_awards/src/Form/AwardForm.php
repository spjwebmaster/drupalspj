<?php
/**
 * @file
 * Contains \Drupal\spj_awards\Form\AwardForm.
 */
namespace Drupal\spj_awards\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AwardForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spj_award_form';
  }

  function getTidByName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = \Drupal::service('entity.repository')->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }

  function getTaxonomyID($name=NULL){
    $vid = 'award_submission_categories';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $tid = null;
    foreach ($terms as $term) {
      if(Strtolower($term->name)== Strtolower($name)){
          $term_data[] = array(
          'id' => $term->tid,
          'name' => $term->name
          );
          $tid = $term->tid;
      }
     
     }


     
     return $tid;

  }

  
  public function buildForm(array $form, FormStateInterface $form_state) {

    $url = $_SERVER['REQUEST_URI']; 
    $urlCheck = substr($url, -1);
    if($urlCheck=="/") {
      $url = substr($url, 0, strlen($url)-1);
    }
    $paths = explode("/",$url);

    $awardName = $paths[count($paths)-1];

    $tax = $this->getTaxonomyID($awardName);
    dpm($tax);



    $price = [];
    switch($awardName){
      case "moe": $price = array(
        'Member' => t('$20.00 per entry for SPJ members'),
		    'Guest' => t('$30.00 per entry for nonmembers')
      );
      break;
      case "sdx": $price = array(
        'Member' => t('$60.00 per entry for SPJ members'),
		    'Guest' => t('$100.00 per entry for nonmembers')
      );
      break;

    }
    

    $main_cat =[
        'Cat1' => t('Category 1'),
		    'Cat2' => t('Category 2'),
        'Cat3' => t('Category 3'),
    ];

    $form['title'] = [
      '#type' => 'fieldset',
      '#title' => $awardName,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#disabled'=>true,
      '#title' => 'Award',
      '#default_value' => $awardName,
    ];


    /*
    $form['user_email'] = array(
      '#type' => 'email',
      '#title' => t('Enter Email Address:'),
      '#required' => TRUE,
    );
    */

    if($awardName=="moe"){
    $form['region'] = [
      '#type' => 'fieldset',
      '#title' => 'Region',
    ];

        $form['region']['region_num'] = array(
          '#type' => 'select',
          '#title' => t('Region Number'),
          '#required' => TRUE,
          '#options' => array(
            'Region1' => t('Region 1'),
            'Region2' => t('Region 2'),
            'Region3' => t('Region 3'),
            'Region4' => t('Region 4'),
            'Region5' => t('Region 5'),
            'Region6' => t('Region 6'),
            'Region7' => t('Region 7'),
            'Region8' => t('Region 8'),
            'Region9' => t('Region 9'),
            'Region10' => t('Region 10'),
            'Region11' => t('Region 11'),
            'Region12' => t('Region 12'),
          ),

        );
      }
      if($awardName=="moe"){

    $form['college'] = [
      '#type' => 'fieldset',
      '#title' => 'College',
    ];

        $form['college']['college_name'] = array(
          '#type' => 'textfield',
          '#title' => t('College or University Name:'),
          '#required' => TRUE,
        );
        $form['college']['college_type'] = array(
          '#type' => 'radios',
          '#title' => t('College Type:'),
          '#required' => TRUE,
          '#options' => array(
            'twoyear' => t('Two Year'),
            'fouryear' => t('Four Year'),
          )
        );
      }


    $form['cat'] = [
      '#type' => 'fieldset',
      '#title' => t('Entry Category'),
    ];

        $form['cat']['main_category'] = array(
          '#type' => 'select',
          '#title' => t('Main Category:'),
          '#description' => t('Entrants should submit a link to the online article or a PDF of the page on which the story appeared.
              The date of publication should be visible. Word documents containing the work will not be accepted. (Cover letters submitted as Word documents are acceptable.)<br /><br />
              PDFs should have the same name as the title of the story. PDFs should be combined into one file, when possible. The cover letter and supporting material should be separated from the main entry. Please note non-Sunday circulation for print publications and specific if your publication is online only. <br /><br /> 
              Supporting material is limited to 5 pages. '),
          '#required' => TRUE,
          '#options' => $main_cat
        );
        $form['cat']['sub_category'] = array(
          '#type' => 'select',
          '#title' => t('Secondary Category:'),
          '#description' => t('izes a reporter or team for deadline reporting of a single event. Entries must have been published in the issue that directly follows the event. Entry must be about a single breaking news event.
          <br /><br />
          Entrants should submit a link to the online article or a PDF of the newspaper page on which the story appeared in print. The date of publication should be visible. Word documents containing the work will not be accepted.
          <br /><br />
          Use non-Sunday circulation number unless it’s a Sunday-only publication. '),
          '#required' => TRUE,
          '#options' => array(
            'Cat1' => t('Cat1'),
            'Cat2' => t('Cat2'),
            'Cat3' => t('Cat3'),
          ),
        );

    $form['entry'] = [
      '#type' => 'fieldset',
      '#title' => t('Entry Information'),
    ];
    

    $form['entry']['entry_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title of Entry:'),
      '#required' => TRUE,
    );
   
    $form['entry']['entry_desc'] = array (
      '#type' => 'textarea',
      '#title' => t('One Sentence Description'),
      '#required' => TRUE,
    );

    $form['entry']['entry_date_published'] = array(
      '#type' => 'textfield',
      '#title' => t('Date(s) Published or Broadcast:'),
      '#required' => TRUE,
    );

    if($awardName=="moe"){
    $form['entry']['entry_broadcast_length'] = array(
      '#type' => 'textfield',
      '#title' => t('Length of Broadcast Entry:'),
    );

    $form['entry']['entry_broadcast_length'] = array(
      '#type' => 'textfield',
      '#title' => t('Length of Broadcast Entry:'),
      '#description' => t('format: "Hours:minutes:seconds"')
    );
    }



    $form['entry']['entry_market_size'] = array(
      '#type' => 'textfield',
      '#title' => t(' Market Size or Circulation:'),
      '#required' => TRUE,
    );

    $form['entrant'] = [
      '#type' => 'fieldset',
      '#title' => t('Entrant(s)'),
    ];

      $form['entrant']['entrant_names'] = array (
        '#type' => 'textarea',
        '#title' => t('Entrant(s):'),
        '#required' => TRUE,
      );


      if($awardName=="moe"){
        $form['media_outlet'] = array(
          '#type' => 'textfield',
          '#title' => t('Name of Media Outlet:'),
          '#required' => TRUE,
        );
        $form['will_mail'] = [
          '#type' => 'checkbox',
          '#title' => t('I will mail all or part of my entry.'),
        ];

    } else {
      $form['mediaorg'] = [
        '#type' => 'fieldset',
        '#title' => t('Media Organization'),
      ];
     
    
        $form['mediaorg']['media_organization'] = array(
          '#type' => 'textfield',
          '#title' => t('Media Organization Name:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_address_street'] = array(
          '#type' => 'textfield',
          '#title' => t('Street Address:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_address_city'] = array(
          '#type' => 'textfield',
          '#title' => t('City:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_address_state'] = array(
          '#type' => 'textfield',
          '#title' => t('State:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_address_zip'] = array(
          '#type' => 'textfield',
          '#title' => t('Zip:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_address_country'] = array(
          '#type' => 'textfield',
          '#title' => t('Country:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_tel'] = array(
          '#type' => 'textfield',
          '#title' => t('Telephone Number:'),
          '#required' => TRUE,
        );
        $form['mediaorg']['media_email'] = array(
          '#type' => 'email',
          '#title' => t('Email:'),
          '#required' => TRUE,
        );
      }

    
    $contactFieldsetTitle = "Contact";
    if($awardName=="moe"){
      $contactFieldsetTitle = "School Year Contact Information";
    }

    $form['contact'] = [
      '#type' => 'fieldset',
      '#title' => t($contactFieldsetTitle),
    ];
        $form['contact']['contact_name'] = array(
          '#type' => 'textfield',
          '#title' => t('Contact Name:'),
          '#required' => TRUE,
        );
        $form['contact']['contact_tel'] = array(
          '#type' => 'textfield',
          '#title' => t('Telephone Number:'),
          '#required' => TRUE,
        );
        $form['contact']['contact_email'] = array(
          '#type' => 'email',
          '#title' => t('Email:'),
          '#required' => TRUE,
        );

        $form['contact']['contact_alt_name'] = array(
          '#type' => 'textfield',
          '#title' => t('Alternate Contact Name:'),
          '#required' => TRUE,
        );
        $form['contact']['contact_alt_tel'] = array(
          '#type' => 'textfield',
          '#title' => t('Alternate Contact Telephone Number:'),
          '#required' => TRUE,
        );


    
    if($awardName=="moe"){
      $form['sunmmer_contact'] = [
        '#type' => 'fieldset',
        '#title' => t('Summer Contact Information'),
      ];
          $form['sunmmer_contact']['sunmmer_contact_address'] = array(
            '#type' => 'textfield',
            '#title' => t('Summer Street Address:'),
            '#required' => TRUE,
          );
      
    }

    if($awardName=="moe"){
    $form['adviser'] = [
      '#type' => 'fieldset',
      '#title' => t('Adviser Information'),
    ];
        $form['adviser']['adviser_name'] = array(
          '#type' => 'textfield',
          '#title' => t('Adviser Name:'),
          '#required' => TRUE,
        );
        $form['adviser']['adviser_tel'] = array(
          '#type' => 'textfield',
          '#title' => t('Telephone Number:'),
          '#required' => TRUE,
        );
        $form['adviser']['adviser_email'] = array(
          '#type' => 'email',
          '#title' => t('Email:'),
          '#required' => TRUE,
        );
      }

    
    $form['price'] = array(
      '#type' => 'radios',
      '#title' => t('SPJ Membership:'),
      '#required' => TRUE,
      '#options' => $price
    );

    



    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );
    return $form;
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*
    if(strlen($form_state->getValue('student_rollno')) < 8) {
      $form_state->setErrorByName('student_rollno', $this->t('Please enter a valid Enrollment Number'));
    }
    if(strlen($form_state->getValue('student_phone')) < 10) {
      $form_state->setErrorByName('student_phone', $this->t('Please enter a valid Contact Number'));
    }
    */
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage(t("Awards Submission Done!! Registered Values are:"));
      foreach ($form_state->getValues() as $key => $value) {
        \Drupal::messenger()->addMessage($key . ': ' . $value);
        }
  }

}