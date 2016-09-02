<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 29/08/16
 * Time: 17:28 PM
 * @package UNO\EvaluacionesBundle\Controller\AdminStats
 */


namespace UNO\EvaluacionesBundle\Controller\AdminStats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

/**
 * Class indexController
 * @package UNO\EvaluacionesBundle\Controller\AdminStats
 */
class indexController extends Controller{

    private $_profile = array();

    /**
     * @Route("/adminStats")
     *
     */
    public function indexAction(Request $request){

        $session = $request->getSession();
        $session->start();

        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseUrl = $this->container->get('router')->getContext()->getBaseUrl();

        $base = $scheme.$host;

        //valida usuario con session iniciada
        if ($session->get('logged_in')) {
            $this->setProfile($session);
            //perfiles permitidos
            if (array_intersect(array('SuperAdmin','Director','COACH'), $this->_profile)) {
                return $this->render('UNOEvaluacionesBundle:AdminStats:index.html.twig',
                    array(
                        'schoolList' => json_decode(file_get_contents("$base/api/v0/catalog/schools", false), true),
                    )
                );
            }else {
                return $this->redirect("/inicio");
            }
        }else{

            return $this->redirectToRoute('login',array(
                'redirect' => 'adminStats',
                'withParams' => 'none'
            ));
        }
    }

    /**
     * @param $session
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setProfile($session){
        $profileJson = json_decode($session->get('profileS'));

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

}