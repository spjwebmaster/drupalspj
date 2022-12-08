<?php 
namespace Drupal\spj_webform\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\WebformSubmissionForm;
/**
 * Provides a 'SPJ Award Webform List' block.
 *
 * @Block(
 *  id = "spj_webform_block",
 *  label = "SPJ Award Webform List",
 *  admin_label = @Translation("SPJ Award Webform List"),
 * )
 */
class SpjWebformBlock extends BlockBase  {

    function build(){


        //Get all submissions of the TV category
        $main_cat = 1234;
        $query = \Drupal::service('webform_query');
        $query->addCondition('main_category', $main_cat);
        $results = $query->execute();
        $data = [];
        foreach($results as $sub){
            $webform_submission_id = $sub->sid;
            $webform_submission = WebformSubmission::load($webform_submission_id);
            
            //get token for link from $webform_submission obj
            $token = $webform_submission->getToken();

            $url = "/webform/moe_entry/" . $webform_submission_id . "?token=" . $token;
            $submissionData = $webform_submission->getData();
            $entry = [];
            $entry["id"] = $webform_submission_id;
            $entry["link"] = $url;
            $entry["data"] = $submissionData;
            
            $data[] = $entry;


        }

        dpm($data);
        /*
        $nodesAll = \Drupal::entityTypeManager()
           ->getStorage('webform')->getQuery();

        //$nodes->condition('type', 'news_item');
        //$nodes->condition('field_legacy_uri', );
        //$nodesAll->condition('title', '%' . 'moe_entry'. '%', "like");
        $nodes = $nodesAll->execute();
        */


        //$webform = Webform::load('moe_entry');
        


        //$data=["a", "b"];
        return [
            
            '#data' => $data,
            '#theme' => 'spj_webform_block',
            '#attached' => [
                'library' => [
                  'spj_webform/webform',
                ]
            ],
        ];
    }

}