<?php
/**
 * @file
 * Contains \Drupal\spj_awards\Form\AwardForm.
 */
namespace Drupal\spj_awards\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

class AwardForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spj_award_form';
  }

  public function formSelectCallback(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
  
    $form['year']['#description'] = "New year desc";
    $response->addCommand(new ReplaceCommand('#field-year', $form['year']));

    $form['month']['#description'] = "New month desc";
    $response->addCommand(new ReplaceCommand('#field-month', $form['month']));
  
    return $response;
  }

  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
    // Prepare our textfield. check if the example select field has a selected option.
    if ($selectedValue = $form_state->getValue('main_category')) {
        // Get the text of the selected option.

        $response = new AjaxResponse();
        //$selectedText = $form['cat']['main_category']['#options'][$selectedValue];

        $vals = $this->getTaxonomyTermsByParent($selectedValue);
        
        //$form['cat']['main_category']['#description'] = $vals['description'][$selectedValue];
        //$form['cat']['main_category']['#title'] = "Update";
        //$response->addCommand(new ReplaceCommand('#field-main-cat', $form['cat']['main_category']));
        
        $form['cat']['sub_category']['#options'] = $vals['data'];
        $response->addCommand(new ReplaceCommand('#field-sub-cat', $form['cat']['sub_category']));
        
      
        return $response;
    }
    
    //return $form['cat']['sub_category']; 
  }

  function getTidByName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }

  function getTaxonomyTermsByParent($parentID = NULL, $vid = NULL) {


    $vid = 'award_submission_categories';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $term_data = array();
    $desc_array = array();
    foreach ($terms as $term) {
      //dpm($term);
      if($term->parents[0] && $term->parents[0]==$parentID){
        $term_data[$term->tid] = $term->name;
        $desc_array[$term->tid] = $term->description__value;
        
      }
      
    }
    //$terms = \Drupal::entityManager()->getStorage('taxonomy_term');
    $returnArray = array();
    $returnArray['data'] = $term_data;
    $returnArray['description'] = $desc_array;

    //dpm($returnArray);

    return $returnArray;

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

  public function yearSelectCallback(array $form, FormStateInterface $form_state) {
    return $form['month'];
  }
  public function monthSelectCallback(array $form, FormStateInterface $form_state) {
    return $form['day'];
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
    
    $terms = $this->getTaxonomyTermsByParent($tax);
    $main_cat = $terms['data'];


    $years = range(2019, 2050);
    $years = array_combine($years, $years);
    $year = $form_state->getValue('year');

    $form['year'] = [
      '#type' => 'select',
      '#title' => $this->t('Year'),
      '#description' => "Year",
      '#options' => $years,
      '#empty_option' => $this->t('- Select year -'),
      '#default_value' => $year,
      '#required' => TRUE,
      '#prefix' => '<div id="field-year">',
      '#suffix' => '</div>',
      '#ajax' => [
        'event' => 'change',
        'callback' => '::formSelectCallback',
        'wrapper' => 'field-month',
      ],
    ];

    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
    $month = $form_state->getValue('month');

    $form['month'] = [
      '#type' => 'select',
      '#title' => $this->t('Month'),
      '#options' => $months,
      '#empty_option' => $this->t('- Select month -'),
      '#default_value' => $month,
      '#required' => TRUE,
      '#description' => "Month",
      '#states' => [
        '!visible' => [
          ':input[name="year"]' => ['value' => ''],
        ],
      ],
      '#prefix' => '<div id="field-month">',
      '#suffix' => '</div>',

    ];

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

    $form['file_upload_details'] = array(
      '#markup' => t('<b>The File</b>'),
    );
	
    $validators = array(
      'file_validate_extensions' => array('pdf'),
    );
    $form['my_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => t('File *'),
      '#multiple' => TRUE,
      '#description' => t('PDF format only'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://awards/'
    );


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
          '#options' => $main_cat,
          '#prefix' => '<div id="field-main-cat">',
          '#suffix' => '</div>',
          '#ajax' => [
            'callback' => '::myAjaxCallback', // don't forget :: when calling a class method.
            //'callback' => [$this, 'myAjaxCallback'], //alternative notation
            'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
            'event' => 'change',
            'wrapper' => 'sub-category', // This element is updated with this AJAX callback.
            'progress' => [
              'type' => 'throbber',
              'message' => $this->t('Updating Category...'),
            ],
          ]
        );

      
        $form['cat']['sub_category'] = array(
          '#type' => 'select',
          '#title' => t('Secondary Category:'),
          '#description' => t('-'),
          '#required' => TRUE,
          '#options' => array(
            'Cat1' => t('Cat1'),
            'Cat2' => t('Cat2'),
            'Cat3' => t('Cat3'),
          ),
          '#prefix' => '<div id="field-sub-cat">',
          '#suffix' => '</div>',
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
  
  /*
  public function validateForm(array &$form, FormStateInterface $form_state) {
 
    if(strlen($form_state->getValue('student_rollno')) < 8) {
      $form_state->setErrorByName('student_rollno', $this->t('Please enter a valid Enrollment Number'));
    }
    if(strlen($form_state->getValue('student_phone')) < 10) {
      $form_state->setErrorByName('student_phone', $this->t('Please enter a valid Contact Number'));
    }
 
  }
  */
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage(t("Awards Submission Done!! Registered Values are:"));
      foreach ($form_state->getValues() as $key => $value) {
        \Drupal::messenger()->addMessage($key . ': ' . $value);
        }
  }

}