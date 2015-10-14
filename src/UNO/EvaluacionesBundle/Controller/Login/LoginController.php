<?php

namespace UNO\EvaluacionesBundle\Controller\Login;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use UNO\EvaluacionesBundle\Controller\Login\Browser;
use UNO\EvaluacionesBundle\Controller\Login\BodyMail;
use UNO\EvaluacionesBundle\Controller\Login\Encrypt;
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Controller\LMS\FilterAPI;
use UNO\EvaluacionesBundle\Entity\Person;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Privilege;
use UNO\EvaluacionesBundle\Entity\Optionapplication;
use UNO\EvaluacionesBundle\Entity\School;
use UNO\EvaluacionesBundle\Entity\Uservalidationemail;
use UNO\EvaluacionesBundle\Entity\Userhttpsession;


class LoginController extends Controller{

    /**
     * Lista de Etatus
     * 1   - ok
     * 97  - Could not resolve host
     * 98  - Not valid JSON string
     * 99  - error de conexion
     * 100 - no existe en LMS
     * 101 - no es un perfil valido
     * 102 - no cuenta con algun periodo activo
     * 103 - no pertenece a Mexico
     * 104 - no esta activo
     * 105 - password invalido
     */

    private $_person;
    private $_personDB;
    private $_user;
    private $_pass;
    private $_datPerson;
    private $_code;

    /**
     * @Route("/")
     */
    public function indexAction(Request $request){
        $session = $request->getSession();
        $session->start();
        $dataBrowser = new Browser();
        if (!$session->get('logged_in')) {
            return $this->render('UNOEvaluacionesBundle:Login:index.html.twig', array(
                'browser' => $dataBrowser->getBrowser(),
                'browserVersion' => $dataBrowser->getVersion()
            ));
        }else{
            return $this->redirect("/inicio");
        }
    }

    /**
     * @Route("/ajax/autentication")
     * @Method("POST")
     */
    public function autenticationAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            if($request->get('login-user')){
                $this->_user = $request->get('login-user');
            }else{
                $this->_user = $this->getCookie();
            }

            $this->_pass = $request->get('login-password');

            if($this->existsUserPassInDB()){
                if($this->_personDB->getActive()){
                    if($this->logIn($request)){
                        $this->addUserHttpSession($request);
                        return new Response('ok');
                    }
                }else{
                    return new Response('104');
                }
            }else{
                if($this->existsUserInDB()){
                    return new Response('105');
                }else{
                    $LMS = new LMS();
                    $api = $LMS->getDataXUserPass($this->_user, $this->_pass, 'http://www.sistemauno.com/source/ws/uno_wsj_login.php');
                    $this->_person = "";
                    if ($this->isObjectAPI($api)) {
                        //validar Permisos
                        $FilterAPI = new FilterAPI($api);
                        $this->_datPerson = $FilterAPI->runFilter($this->_user, $this->_pass);
                        if (is_array($this->_datPerson)) {
                            //si el usuario cuenta con un perfil apropiado se le pide que valide sus coreo
                            $this->validateEmailUser();
                            $this->sendMail();
                            return new Response("1|" . $this->_datPerson['email'] . "|" . $this->_datPerson['personId'] . "|" . $this->_code . "|" . $this->_datPerson['name']);
                        } else {
                            return new Response($this->_datPerson);
                        }
                    } else {
                        return new Response($api);
                    }
                }
            }
        }else{
            return new response($request->getMethod());
        }
    }

    public function forwardMailAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $BodyMail = new BodyMail();
            $_name = $request->get('name');
            $_code = $request->get('code');
            $_email = $request->get('email');
            $_personId = $request->get('personId');

            $this->updateMailValidation($_personId, $_email);

            $to = $_email;
            //$to = 'potcheunam@gmail.com';
            $url = "http://dev.evaluaciones.unoi.com/app_dev.php/linkCode?code=".base64_encode($_code)."&email=".base64_encode($_email);
            $subject = "Validación de Email";
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: Red UNOi <noreplymx@unoi.com>'."\r\n";
            $headers .= 'Reply-To: NoReply <noreplymx@unoi.com>' . "\r\n";
            $headers .= 'Bcc: potcheunam@gmail.com' . "\r\n";
            $message = $BodyMail->run($_name, $_code, $url);
            mail($to, $subject, $message, $headers, '-f noreplymx@unoi.com');
            return new response('1');
        }
    }

    private function sendMail() {
        $BodyMail = new BodyMail();
        $to = $this->_datPerson['email'];
        //$to = 'potcheunam@gmail.com';

        $url = "http://dev.evaluaciones.unoi.com/app_dev.php/linkCode?code=".base64_encode($this->_code)."&email=".base64_encode($this->_datPerson['email']);

        $subject = "Validación de Email";
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: Evaluacione Red UNOi <noreplymx@unoi.com>'."\r\n";
        $headers .= 'Reply-To: NoReply <noreplymx@unoi.com>' . "\r\n";
        $headers .= 'Bcc: potcheunam@gmail.com' . "\r\n";
        $message = $BodyMail->run($this->_datPerson['name'], $this->_code, $url);
        mail($to, $subject, $message, $headers, '-f noreplymx@unoi.com');
    }

    /**
     * @return bool
     */
    private function existsUserInDB(){
        $em = $this->getDoctrine()->getManager();
        $this->_personDB = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('user' => $this->_user));
        if ($this->_personDB) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool
     */
    private function existsUserPassInDB(){
        $encrypt = encrypt::encrypt($this->_pass);
        //$this->decrypt($encrypt);
        //exit();
        $em = $this->getDoctrine()->getManager();
        $this->_personDB = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('user' => $this->_user, 'password' => $encrypt));

        if (!empty($this->_personDB)) {
            return true;
        }else{
            return false;
        }
    }

    private function getPrivilege(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('O.nameOptionApplication, O.ruteOptionApplication, O.iconOptionApplication')
            ->from('UNOEvaluacionesBundle:Person', 'P')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','P1','WITH', 'P.personid = P1.personid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','P2','WITH', 'P1.profileid = P2.profileid')
            ->innerJoin('UNOEvaluacionesBundle:Privilege','P3','WITH', 'P2.profileid = P3.profileId')
            ->innerJoin('UNOEvaluacionesBundle:Optionapplication','O','WITH', 'O.optionApplicationId = P3.optionApplicationId')
            ->where('P.personid = :personId')
            ->andWhere('O.statusOptionApplication = 1')
            ->setParameter('personId', $this->_personDB->getPersonid())
            ->groupBy('O.nameOptionApplication, O.ruteOptionApplication, O.iconOptionApplication')
            ->orderBy('O.optionApplicationId')
            ->getQuery()
            ->getResult();
        //$p = $q->execute();
        //print_r( json_encode($q) );
        //exit();
        return json_encode($q);
    }

    private function getProfile(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('P2.profileid, P2.profilecode, P2.profile')
            ->from('UNOEvaluacionesBundle:Person', 'P')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','P1','WITH', 'P.personid = P1.personid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','P2','WITH', 'P1.profileid = P2.profileid')
            ->where('P.personid = :personId')
            ->setParameter('personId', $this->_personDB->getPersonid())
            ->getQuery()
            ->getResult();
        return json_encode($q);
    }

    private function logIn($request){
        $session = $request->getSession();
        $session->start();
        // set and get session attributes
        $session->set('logged_in', true);
        $session->set('personIdS', $this->_personDB->getPersonid());
        $session->set('nameS', $this->_personDB->getName());
        $session->set('privilegeS', $this->getPrivilege());
        $session->set('profileS', $this->getProfile());
        $this->setCookie();
        return true;
    }

    public function logoutAction(Request $request) {
        $session = $request->getSession();
        $session->clear();
        $session->remove('logged_in');
        $session->remove('personIdS');
        $session->remove('nameS');
        $session->remove('authorized_in');
        $session->invalidate();
        unset($session);

        return $this->redirect('/');
    }

    private function setCookie() {
        $response = new Response();
        $cookie = new Cookie('login-user', $this->_user, time() + 3600 * 24, '/', 'unoi.com', false, false);
        $response->headers->setCookie($cookie);
        $response->send();
    }


    private function getCookie() {
        $request = $this->get('request');
        $cookies = $request->cookies;

        if ($cookies->has('login-user')) {
            return $cookies->get('login-user');
        }else{
            return false;
        }
    }

    private function isObjectAPI($api){
        $dat = json_decode($api);
        if (is_object($dat)) {
            return true;
        }else{
            return false;
        }
    }

    private function validateEmailUser(){
        $this->deleteDuplicateCode($this->_datPerson['personId'], $this->_datPerson['email']);
        $this->_code = rand(10000, 99999);
        $em = $this->getDoctrine()->getManager();
        $UserValidationEmail = new Uservalidationemail();
        $UserValidationEmail->setEmail($this->_datPerson['email']);
        $UserValidationEmail->setCode($this->_code);
        $UserValidationEmail->setDateregister(new \DateTime());
        $UserValidationEmail->setData(json_encode($this->_datPerson));
        $UserValidationEmail->setPersonid($this->_datPerson['personId']);

        $em->persist($UserValidationEmail);
        $em->flush();
    }

    public function deleteDuplicateCode($personId, $email) {
        $em = $this->getDoctrine()->getManager();
        $UserValidationEmail = $em->getRepository('UNOEvaluacionesBundle:Uservalidationemail')->findOneBy(array('personid' => $personId));
        if ($UserValidationEmail) {
            $em->remove($UserValidationEmail);
            $em->flush();
        }
    }

    private function updateMailValidation($personId, $email){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->update('UNOEvaluacionesBundle:Uservalidationemail', 'u')
            ->set('u.email', $qb->expr()->literal($email))
            ->where('u.personid = ?1')
            ->setParameter(1, $personId)
            ->getQuery();
        $p = $q->execute();
    }

    private function addUserHttpSession($request){
        //inserta nueva UserHttpSession
        $dataBrowser = new Browser();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        try{
            $Userhttpsession = new Userhttpsession();
            $Userhttpsession->setBrowser($dataBrowser->getBrowser());
            $Userhttpsession->setBrowserversion($dataBrowser->getVersion());
            $Userhttpsession->setPlatform($dataBrowser->getPlatform());
            $Userhttpsession->setSid($session->getId());
            $Userhttpsession->setIpcliente($_SERVER['REMOTE_ADDR']);
            $Userhttpsession->setLoggedin(1);
            $Userhttpsession->setStartsession(new \DateTime());
            $Userhttpsession->setPersonid($this->_personDB->getPersonid());

            $em->persist($Userhttpsession);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            print_r($e->getMessage());
            return false;
        }
    }

    public function testAction(){
        return $this->render('UNOEvaluacionesBundle:Login:test.html.twig');
    }

}