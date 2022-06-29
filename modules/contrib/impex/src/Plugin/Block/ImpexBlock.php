<?php 
namespace Drupal\impex\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\impex\ImpexCreds;
use Drupal\impex\ImpexSalutation;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'Impexium Membership Block' block.
 *
 * @Block(
 *  id = "impex_membership_block",
 *  label = "Impexium Membership",
 *  admin_label = @Translation("Impexium Membership Block"),
 * )
 */
class ImpexBlock extends BlockBase implements ContainerFactoryPluginInterface  {

   /**
   * @var Drupal\impex\ImpexCreds;
   */
  protected $creds;

 /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param Drupal\impex\ImpexCreds
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ImpexCreds $creds) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->creds = $creds;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('impex.creds')
    );
  }
    

    function build(){
        $credArr = $this->creds->getCreds();
    
        $email =  $this->creds->getCurrentUserEmail();

        if($email){
        //$userInfo = impex_get_impexium_user($email);

        $userInfo = $this->creds->get_impexium_user($email);

        if($userInfo){
            $memberID = $userInfo->recordNumber;
            $memberName = $userInfo->firstName . " " . $userInfo->lastName;
            $preferredFirstName = $userInfo->preferredFirstName;
            $memberImage = $userInfo->imageUri;
            $session = \Drupal::request()->getSession();
            $fetchsso = $session->get('usersso');
            
            $data = array(
                "email"=>               $email,
                "name" =>               $memberName,
                "preferredFirstName"=>  $preferredFirstName,
                "image" =>              $memberImage,
                "memberID" =>           $memberID,
                "credArr" =>            $credArr,
                "ssoToken"=>            $fetchsso

            );
            

        } else {
            $msg = "Nope";

            $data = array(
                "body"=> $msg,
            );
        }
        } else {
            $data = null;
        }

       
        return [
            '#theme' => 'impex_block',
            '#data' => $data
            
        ];

    }
}