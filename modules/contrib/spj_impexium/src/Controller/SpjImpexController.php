<?php
namespace Drupal\spj_impexium\Controller;

use Drupal\spj_impexium\SpjImpexCreds;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SpjImpexController extends ControllerBase {

    /**
   * @var Drupal\spj_impexium\SpjImpexCreds;
   */
    protected $creds;


    /**
     * ImpexCreds constructor
     * @param \Drupal\impex\SpjImpexCreds
     */
    public function __construct(SpjImpexCreds $creds ){
        $this->creds = $creds;
    }

    /**
     * {@inheritDoc}
     */
    public static function create(ContainerInterface $container){
        return new static($container->get('spj_impexium.creds'));
    }


    public function spj_impex_send_request( $url, $data, $customHeaders = null){ 
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
    

    public function getData(Request $request){

        $method = "";
        $method = $request->get("type");
        $credArr = $this->creds->getCreds();
        //dd($credArr);

        $dataAr = array(
        'AppName' => $credArr['APP_NAME'],
        'AppKey' => $credArr['APP_KEY']
        );

        $msg = "loaded " . $credArr['ACCESS_END_POINT'] . " | ". $credArr['APP_KEY'];
        $data = $this->spj_impex_send_request($credArr['ACCESS_END_POINT'], $dataAr);
        
        
        if($data!=null){
            
            $apiEndPoint = $data->uri;
            $accessToken = $data->accessToken;
            
            //Step 2: Get AppToken or UserToken or Both
            //POST api/v1/Signup/Authenticate
            $dataArray = array(
            'AppId' => $credArr['APP_ID'],
            'AppPassword' => $credArr['APP_PASSWORD'],
            'AppUserEmail' => $credArr['APP_USER_EMAIL'],
            'AppUserPassword' => $credArr['APP_USER_PASSWORD'],
            );

            
            $dat = $this->spj_impex_send_request($apiEndPoint, $dataArray, array(
            'accesstoken: ' . $accessToken,
            ));

            $msg .= "|yay!";
            //dd($dat);

            

        } else {
            $msg .= "|Cannot found";
        }
 
            
        //dd($credArr);
        return ["hi"=>"there", "obj"=>$method, "msg"=>$msg];
    }
   
    public function index(Request $request){

        return new JsonResponse([ 'data' => $this->getData($request), 'method' => 'GET', 'status'=> 200]);
        //return "hello " . $method;
    }


    
}


