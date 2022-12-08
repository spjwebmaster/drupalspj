<?php 
namespace Drupal\spj_webform_payment\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\commerce\commerce_product;
use Drupal\commerce;
use Drupal\commerce_cart;
use Drupal\commerce_cart\CartManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use \GuzzleHttp\RequestOptions;


class SpjWebformPaymentController extends ControllerBase {


    private function checkIfNodeExists($data, $type){
        $title = $data->title;
        if($title){
            $nodes = \Drupal::entityTypeManager()
                ->getStorage('node')
                ->loadByProperties([
            'title' => $title,
            ]);
            if(count($nodes)>0){
                return $title . " exists. Skipping<br />";
            } else {
                $newNid =  $this->createNode($data,$type);
                return "[going to create '" . $title . "' in the future]" .  $newNid. "<br />";
            }
        }
    }



    private function createNode($data, $type){

        $machineType = $type;

        $retId = "";
        $dataArray = null;
      

            $title = trim($data->title);

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_role' => array(
                    'target_id' => 1
                ),
                'field_twitter' => trim($data->field_twitter),
                'field_email' => trim($data->field_email),
                'field_title' => trim($data->field_title),
                'body' => array(
                    'value' => $data->body,
                    'format' => 'full_html',
                )
            );


            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;

      
    }

   /**
    * The cart manager.
    *
    * @var \Drupal\commerce_cart\CartManagerInterface
    */
    protected $cartManager;
    
    /**
    * The cart provider.
    *
    * @var \Drupal\commerce_cart\CartProviderInterface
    */
    protected $cartProvider;
    
    /**
    * Constructs a new CartController object.
    *
    * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
    *   The cart provider.
    */
    public function __construct(CartManagerInterface $cart_manager,CartProviderInterface $cart_provider) {
    $this->cartManager = $cart_manager;
    $this->cartProvider = $cart_provider;
    }
    
    /**
    * {@inheritdoc}
    */
    public static function create(ContainerInterface $container) {
        return new static(
        $container->get('commerce_cart.cart_manager'),
        $container->get('commerce_cart.cart_provider')
        );
    }
 



    public function index(Request $request){

        $action = "complete";
        if($request->query->get("action")){
            $action = $request->query->get("action");
        }

        $award = "moe";
        if($request->query->get("award")){
            $action = $request->query->get("award");
        }

        $title = "Title";
        if($request->query->get("title_of_entry")){
            $title = $request->query->get("title_of_entry");
        }
        $things = $request->getContent();
        //$things= str_replace("\u003E", "", $things);
        //$things= str_replace("\n", "", $things);
        $allThings = explode("&", $things);

        $allData = [];

        foreach($allThings as $param){
            $paramsplit = explode("=", $param);
            $allData[$paramsplit[0]] = $paramsplit[1];
        }

        $message = "Award PMT Controller Hit";

        

        $productId = 2;
        // award fee product
       
      

        //$destination = \Drupal::service('path.current')->getPath();
        $productObj = Product::load($productId);
      
        //$product_variation_id = $productObj->get('variations')
          //->getValue()[0]['target_id'];
          
        
        $product_variation_id = 0;

        switch($award){
            case "moe": 
                if($allData["are_you_an_active_spj_member_"] == "yes"){
                    $product_variation_id = 3;
                } else {
                    $product_variation_id = 4;
                }
                break;
        }

        $allData['product_variation_id'] = $product_variation_id;


        $variationobj = \Drupal::entityTypeManager()
          ->getStorage('commerce_product_variation')
          ->load($product_variation_id);

       
        $storeId = 1;
        $store = \Drupal::entityTypeManager()
          ->getStorage('commerce_store')
          ->load($storeId);

        $cart = $this->cartProvider->getCart('default', $store);
        if (!$cart) {
            $cart = $this->cartProvider->createCart('default', $store);
         
        }
        //$line_item_type_storage = \Drupal::entityTypeManager()
        //    ->getStorage('commerce_order_item_type');
        $cart_manager = \Drupal::service('commerce_cart.cart_manager');
        
        //$line_item = $cart_manager->addEntity($cart, $variationobj);
        //$res = CartManager::addEntity($cart, $variationobj);


        $params = print_r($allData, true);
        //$storeId = $productObj->get('stores')->getValue()[0]['target_id'];
        /*
        
        // Process to place order programatically.

       
        $response = new RedirectResponse(Url::fromRoute('commerce_cart.page')->toString());
       
       
       */

        $message .= " | action " . $action . " Title: " . $params;
        
        \Drupal::logger('spj_webform_payment')->warning($message);

        //return $response;
        /*

        return [
            '#markup' => t($message)
        ];
        */
        
        return new JsonResponse([ 'data' => $message, 'method' => 'GET', 'status'=> 200]);
        
         
    }

}