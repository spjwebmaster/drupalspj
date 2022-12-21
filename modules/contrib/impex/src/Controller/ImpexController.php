<?php
namespace Drupal\impex\Controller;

use Drupal\impex\ImpexCreds;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;

class ImpexController extends ControllerBase {

    /**
   * @var Drupal\impex\ImpexCreds;
   */
    protected $creds;


    /**
     * ImpexCreds constructor
     * @param \Drupal\impex\ImpexCreds
     */
    public function __construct(ImpexCreds $creds ){
        $this->creds = $creds;
    }

    /**
     * {@inheritDoc}
     */
    public static function create(ContainerInterface $container){
        return new static($container->get('impex.creds'));
    }

   
    public function hello(){
        return "hello";
    }

    public function membershipredirect(){
        

        $email = $this->creds->getCurrentUserEmail();
       
        if($email){
            $credArr = $this->creds->getCreds();
            $userInfo = $this->creds->get_impexium_user($email);
            //dpm($userInfo);
            if($userInfo){


                $session = \Drupal::request()->getSession();

                $fetchsso = $session->get('usersso');

                $site =  \Drupal::request()->getHost();
                $port = $_SERVER['SERVER_PORT'] ? ':'.$_SERVER['SERVER_PORT'] : '';
                //dpm($port);
                if($port==":8080"){
                    $site .=$port;
                }
                $site .=  "/membership";

                $redir = "https://my.spj.org/account/login.aspx?sso=" . $fetchsso . "&RedirectUrl=" .$site;
                //$redirUrl = Url::fromUri($redir);

                return new TrustedRedirectResponse($redir);
                

            } else {
                return new RedirectResponse(Url::fromRoute('<front>')->setAbsolute()->toString());
            }
            
        } else {

            return new RedirectResponse(Url::fromRoute('<front>')->setAbsolute()->toString());
        }
    }

    
}


