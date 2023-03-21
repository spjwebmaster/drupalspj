<?php
namespace Drupal\spj_impexium;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormState;



class SpjImpexCreds {

    use StringTranslationTrait;

    public function getCreds(){

        $formID = 'spj_impexium_creds_configuration_form';
        $form_state = new FormState();
        $form_state->setRebuild();


        $config = \Drupal::config('spj_impexium.creds');
        
        $dat = array();
        $dat["ACCESS_END_POINT"] = $config->get("ACCESS_END_POINT");
        $dat["APP_NAME"] = $config->get("APP_NAME");
        $dat["APP_KEY"] = $config->get("APP_KEY");
        $dat["APP_ID"] = $config->get("APP_ID");
        $dat["APP_PASSWORD"] = $config->get("APP_PASSWORD");
        $dat["APP_USER_EMAIL"] = $config->get("APP_USER_EMAIL");
        $dat["APP_USER_PASSWORD"] = $config->get("APP_USER_PASSWORD");
        $dat["CURRENT_USER_EMAIL"] = $config->get("CURRENT_USER_EMAIL");
        $dat["CURRENT_USER_PASSWORD"] = $config->get("CURRENT_USER_PASSWORD");

        SpjImpexCreds::setCreds($dat);
        return $dat;
    }
    private function setCreds($dat){
        define("ACCESS_END_POINT", $dat["ACCESS_END_POINT"]);
        define("APP_NAME", $dat["APP_NAME"]);
        define("APP_KEY", $dat["APP_KEY"]);

        define("APP_ID", $dat["APP_ID"]);
        define("APP_PASSWORD",  $dat["APP_PASSWORD"]);
        define("APP_USER_EMAIL",  $dat["APP_USER_EMAIL"]);
        define("APP_USER_PASSWORD",  $dat["APP_USER_PASSWORD"]);
        define("CURRENT_USER_EMAIL", "");
        define("CURRENT_USER_PASSWORD", "");
    }

    function getCurrentUserEmail(){
        if(\Drupal::currentUser()->id()) {
      
            $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
            
            $email = $user->get('mail')->value;
            if($email==null || $email ==""){
                $email = $user->get('name')->value;
            }
      
            $email = str_replace("impexium_","",$email);
            return $email;
      
        } else {
            return null;
        }
      
      }

    public function send_request( $url, $data, $customHeaders = null){ 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if ($customHeaders !== null ) {
        $headers = $customHeaders;
      }
      else { 
        $headers = [];
      }
    
      if ($data === null ) {
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET");
      }
      else { 
        curl_setopt( $ch , CURLOPT_CUSTOMREQUEST, "POST");
        $json = json_encode($data);
        $headers[] = 'Content-Length: ' . strlen($json);
        $headers[] = 'Content-Type: application/json; charset=utf-8';
    
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $json);
        }
        
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers);
    
        $ret = curl_exec($ch);
        $httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # http response status code
        
        return json_decode($ret);
    }
    

    public function get_impexium_user($email){
        $apiEndPoint = "";
        $baseUri = "";
        $accessToken = "";
        $appToken = "";
        $userToken = "";
        $userId = "";

        //Step 1 : Get ApiEndPoint and AccessToken
        //POST api/v1/WebApiUrl
        $data = array(
            'AppName' => APP_NAME,
            'AppKey' => APP_KEY
        );
        $data = SpjImpexCreds::send_request(ACCESS_END_POINT, $data);

        $apiEndPoint = $data->uri;
        $accessToken = $data->accessToken;

        //Step 2: Get AppToken or UserToken or Both
        //POST api/v1/Signup/Authenticate
        $data = array(
            'AppId' =>APP_ID,
            'AppPassword' => APP_PASSWORD,
            'AppUserEmail' => APP_USER_EMAIL,
            'AppUserPassword' => APP_USER_PASSWORD
        );
        $data = SpjImpexCreds::send_request($apiEndPoint, $data, array(
            'accesstoken: ' . $accessToken,
        ));
        $appToken = $data->appToken;
        $baseUri = $data->uri;
        $userToken = $data->userToken;
        

        $baseUri = "https://my.spj.org/api/v1/";

        // find by sso token instead?

        $urlPath = "Individuals/FindByEmail/" . $email ."/?includeDetails=true";
        $userdata = SpjImpexCreds::send_request($baseUri . $urlPath, null, array(
            'usertoken: ' . $userToken,
            'apptoken: ' . $appToken,
        ));

        if($userdata->dataList[0]){
            $thisUser = $userdata->dataList[0];
            
            return $thisUser;
        } else {
            return null;
        }
    }

}
