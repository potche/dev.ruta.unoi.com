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

    private $_person;

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
                echo "<h1>$this->user, si esta en la BD.<br/> Dejar pasar y asignar permisos</h1>";
            }else{
                $LMS = new LMS();
                $api = $LMS->getDataXUserPass($this->user,$this->pass,'http://homol.sistemauno.com/ws/uno_wsj_login.php');
                $this->_person = $this->getDatPerson($api);
                if (is_object($this->_person)) {
                    #si existe el usuario hay que guardarlo en la bd y validar su perfil....
                    $this->altaUser();
                }else{
                    return new Response($api);
                }


            }
        }else{
            return new response($request->getMethod());
        }
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
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

    private function getDatPerson($api){
        $dat = json_decode($api);
        return $dat->person;
    }

    private function valAPI(){

    }

    private function validateEmailUser($api){

    }

    private function altaUser(){

        print_r($this->_person->personId);
        exit();
        $em = $this->getDoctrine()->getManager();
        $Person = new Person();
        $Person->setPersonId($api->personId);
        $Person->setUsername($api->username);
        $Person->setNombre($api->name);
        $Person->setApaterno($api->surname);
        $Person->setCorreo($api->email);
        $Person->setPassword($this->getSessionPassword());
        $Person->setCreacion(new \DateTime());
        $Person->setUltacceso(new \DateTime());
        $Person->setActivo(1);
        $Person->setIdlms($api->personaId);
        $Person->setLanguageCode($api->languageCode);
        $Person->setLanguage($api->language);
        $Person->setToken($this->getSessionToken());
        $Person->setBirthDay($api->birthDay);
        $Person->setBirthMonth($api->birthMonth);
        $Person->setBirthYear($api->birthYear);
        $Person->setAdmin(0);

        $em->persist($Person);
        $em->flush();
        if ($Person) {
            return true;
        }else{
            return false;
        }
    }

}