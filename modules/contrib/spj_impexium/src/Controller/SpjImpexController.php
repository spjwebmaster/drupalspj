<?php
namespace Drupal\spj_impexium\Controller;

use Drupal\spj_impexium\SpjImpexCreds;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
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
        $dat = null;
        
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


            $appToken = $dat->appToken;
            $userToken = $dat->userToken;


            $committeeID = "02b2269e-f3a7-4e1d-b269-91b8ab0632d5";
            if($request->get("id")){
                $committeeID = $request->get("id");
            } 

            $baseUri = "https://my.spj.org/api/v1/";
            $urlFetch = "/Committees" . "/" . $committeeID . "/Members/1";

            $commdata = $this->spj_impex_send_request($baseUri . $urlFetch, null, array(
                'usertoken: ' . $userToken,
                'apptoken: ' . $appToken,
            ));
 

            $msg .= "|yay!";
            //dd($dat);

            
            return [$commdata, "obj"=>$method, "msg"=>$msg];

        } else {
            $msg .= "|Cannot found";

            return [$dat, "obj"=>$method, "msg"=>$msg];
        }
 
            
        //dd($credArr);
        
    }
   
    public function index(Request $request){

        return new JsonResponse([ 'data' => $this->getData($request), 'method' => 'GET', 'status'=> 200]);
        //return "hello " . $method;
    }

    private function createNode($data, $type){

        $machineType = $type;
        $id = "tomake";
        return  $id;

    }

    private function getNodes($type){

        $temp = "";
            $nodes = \Drupal::entityTypeManager()
                ->getStorage('node')->getQuery();
            $nodes->condition('type', $type);
            $nids = $nodes->execute();

            /*
            $urlToload = "https://spj.org/CommitteesChapters.csv";
            $CSVfp = fopen($urlToload, "r");
            $count = 0;
            $counter = 1;

            
            if($CSVfp !== FALSE) {
                while(! feof($CSVfp)) {
                    
                    $data = fgetcsv($CSVfp, 1000, ",");
                    
                    if($count>0){
                        $id = $data[7];
                        $code = $data[9];
                        if($code!=null && $code!=" "){
                            $temp .= $id . " to load " . $code . "<br />";

                            $tnode = Node::load($id);
                            $tnode->field_committee_code->value = $code;
                            $tnode->save();
                        } else {
                            $temp .= $id . " <code>no code</code><br />";
                        }
                        


                        
                    }
                    $count++;
                }
            }
            fclose($CSVfp);
            */

                                    
            foreach($nids as $nid){
                $tnode = Node::load($nid);
                $temp .= "<h3>" . $tnode->get("title")->value . "</h3>";
                
                $committeeId = $tnode->get("field_committee_code")->value;
                if( $committeeId!=null && $committeeId!=""){
                    $temp .= "<div>" . $committeeId . "</div>";

                    /*
                    $url  = "https://support.spjnetwork.org/getData.php?c=" . $committeeId;
                    $json = file_get_contents($url);
                    $temp .= "<pre>" . $json . "</pre>";
                    */

                } else {
                    $temp .= "<code>NO COMMITTEE FOUND</code>";
                }
                
                $temp .= "<hr />";
                //$tnode->save();
                //$temp .= $nid . ", ";
            }
                
            return $temp;
        
    }

    public function import(Request $request){

        $ret = $this->getNodes("chapter");

        return [
            '#markup' => 'Import Page<br />' . $ret,
            '#attached' => [
                'library' => [
                  'spj_impexium/api',
                ]
            ],
        ];
        
    }


    
}


