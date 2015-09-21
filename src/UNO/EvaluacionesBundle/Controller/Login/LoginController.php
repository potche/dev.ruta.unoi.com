<?php

namespace UNO\EvaluacionesBundle\Controller\Login;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use UNO\EvaluacionesBundle\Controller\Login\Browser;
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Entity\Person;

class LoginController extends Controller{
    /**
     * @Route("/")
     */
    public function indexAction(){
        $dataBrowser = new Browser();

        return $this->render('UNOEvaluacionesBundle:Login:index.html.twig', array(
            'browser' => $dataBrowser->getBrowser(),
            'browserVersion' => $dataBrowser->getVersion()
        ));

    }

    /**
     * @Route("/ajax/autentication")
     * @Method("POST")
     */
    public function autenticationAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $this->user = $request->get('login-user');
            $this->pass = $request->get('login-password');

            $existsUserInDB = $this->existsUserInDB($this->user,$this->pass);

            if($existsUserInDB){
                echo "<h1>$this->user, si esta en la BD</h1>";
            }else{
                echo "<h1>$this->user, no esta dado de alta</h1>";
                $LMS = new LMS();
                $api = $LMS->getDataXUserPass($this->user,$this->pass,'http://homol.sistemauno.com/ws/uno_wsj_login.php');
                return new Response(json_encode($api));
            }
        }else{
            return new response($request->getMethod());
        }
    }

    private function existsUserInDB($username,$password){
        $em = $this->getDoctrine()->getManager();
        $Person = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('user' => $username, 'password' => $password));
        if ($Person) {
            return true;
        }else{
            return false;
        }
    }

    private function dataBrowser(){
        $dataBrowser = new Browser();
        $dataBrowser->getBrowser();
        $dataBrowser->getVersion();
        $dataBrowser->getPlatform();
    }

}