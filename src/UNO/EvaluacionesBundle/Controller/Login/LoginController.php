<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 17/09/15
 * Time: 10:28 AM
 * Class Login
 * @package UNO\EvaluacionesBundle\Controller\Login
 */

namespace UNO\EvaluacionesBundle\Controller\Login;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UNO\EvaluacionesBundle\Controller\Login\Browser;
use UNO\EvaluacionesBundle\Controller\Login\BodyMail;
use UNO\EvaluacionesBundle\Controller\Login\Mcrypt;
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Controller\LMS\FilterAPI;
use UNO\EvaluacionesBundle\Entity\Person;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Privilege;
use UNO\EvaluacionesBundle\Entity\Optionapplication;
use UNO\EvaluacionesBundle\Entity\School;
use UNO\EvaluacionesBundle\Entity\Uservalidationemail;
use UNO\EvaluacionesBundle\Entity\Userhttpsession;
use UNO\EvaluacionesBundle\Entity\Cversion;
use UNO\EvaluacionesBundle\Entity\Feature;

DEFINE('logged_in', 'logged_in');
DEFINE('login_user', 'login-user');
DEFINE('personId', 'personId');
DEFINE('email', 'email');
DEFINE('PersonDB_L', 'UNOEvaluacionesBundle:Person');
DEFINE('success', 'success');
DEFINE('baseUri', 'http://dev.ruta.unoi.com/');

/**
 * Class LoginController
 * @package UNO\EvaluacionesBundle\Controller\Login
 */
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

    private $_request;
    /**
     * @var
     */
    private $_person;
    /**
     * @var
     */
    private $_personDB;
    /**
     * @var
     */
    private $_user;
    /**
     * @var
     */
    private $_pass;
    /**
     * @var
     */
    private $_datPerson;
    /**
     * @var
     */
    private $_code;
    /**
     * @var
     */
    private $_response;

    /**
     * @Route("/")
     *
     * Muestra el formulario de Login
     */
    public function indexAction(Request $request){

        $session = $request->getSession();

        $redir = $request->get('redirect') == null ? 'none' : $request->get('redirect');
        $with = $request->get('with') == null ? 'none' : $request->get('with');

        $session->start();
        $dataBrowser = new Browser();
        if (!$session->get(logged_in)) {
            return $this->render('UNOEvaluacionesBundle:Login:index.html.twig', array(
                'browser' => $dataBrowser->getBrowser(),
                'browserVersion' => $dataBrowser->getVersion(),
                'redir' => $redir,
                'with' => $with
            ));
        }else{

            return $this->redirectToRoute("inicio");
        }
    }

    /**
     * @Route("/ajax/autentication")
     * @Method("POST")
     * inicia la Autenticacion
     * de los datos obtenidos en le formulario
     */
    public function autenticationAction(Request $request) {
        $this->_request = $request;
        #se valida que la peticion sea de tipo POST
        if ($request->getMethod() == 'POST') {
            $this->setUserPass();
            //revisamos que el usuario este en la base de datos comparando su User y Pass
            $this->existsUserPassInDB();
            if(!empty($this->_personDB)){
                //user existente
                $this->valUserExisting();
            }else if( !empty($this->existsUserInDB()) ){
                if($this->valPassLMS()){
                    $this->existsUserPassInDB();
                    if(!empty($this->_personDB)) {
                        //user existente
                        $this->valUserExisting();
                    }
                }else{
                    $this->_response = '105';
                }
            }else{
                $this->preAddUser();
            }

            return new response($this->_response);
        }else{
            return new response($request->getMethod());
        }
    }

    /**
     * inicializa las variables
     * login-user
     * login-password
     * enviadas del formulario
     */
    private function setUserPass(){
        if($this->_request->get(login_user)){
            $this->_user = $this->_request->get(login_user);
        }else{
            $this->_user = $this->getCookie();
        }
        $this->_pass = $this->_request->get('login-password');
    }

    /**
     * valida si el usuario esta activo
     * si lo esta, lo logea
     * en caso contrario envia el error
     */
    private function valUserExisting(){
        if($this->_personDB->getActive()){
            if($this->logIn()){
                $this->addUserHttpSession();
                $this->_response = 'ok';
            }
        }else{
            $this->_response = '104';
        }
    }

    /**
     * prepara los datos para la alta del usuario
     */
    private function preAddUser(){
        $LMS = new LMS();
        $api = $LMS->getDataXUserPass($this->_user, $this->_pass, 'https://www.sistemauno.com/source/ws/uno_wsj_login.php');
        $this->_person = "";
        if ($this->isObjectAPI($api)) {
            //validar Permisos
            $FilterAPI = new FilterAPI($api);
            $this->_datPerson = $FilterAPI->runFilter($this->_user, $this->_pass);
            if (is_array($this->_datPerson)) {
                //si el usuario cuenta con un perfil apropiado se le pide que valide sus coreo
                $this->validateEmailUser();
                if($this->validUniqueMail()){
                    $this->sendMail();
                    $this->_response = "1|" . $this->_datPerson[email] . "|" . $this->_datPerson[personId] . "|" . $this->_code . "|" . $this->_datPerson['name'];
                }else{
                    $this->_response = "2|" . $this->_datPerson[email] . "|" . $this->_datPerson[personId] . "|" . $this->_code . "|" . $this->_datPerson['name'];
                }
            } else {
                $this->_response = $this->_datPerson;
            }
        } else {
            $this->_response = $api;
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * la alta del usuario fue exitosa
     */
    public function successAction(Request $request){
        $session = $request->getSession();
        $session->start();
        if ($session->get(logged_in) && $session->get(success)) {
            $session->set(success, false);
            return $this->render('UNOEvaluacionesBundle:Login:success.html.twig');
        }else{
            return $this->redirect("/");
        }
    }

    /**
     * @param Request $request
     * @return Response
     * reenvia el correo a validar
     * pueden haber cambiado el correo
     */
    public function forwardMailAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $BodyMail = new BodyMail();
            $_name = $request->get('name');
            $_code = $request->get('code');
            $_email = $request->get(email);
            $_personId = $request->get(personId);

            $this->updateMailValidation($_personId, $_email);

            $to = $_email;
            $url = baseUri."linkCode?code=".base64_encode($_code)."&email=".base64_encode($_email);
            $subject = "DEV-Validación de Email";
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

    /**
     * envia el correo a validar
     */
    private function sendMail() {
        $BodyMail = new BodyMail();
        //$to = $this->_datPerson[email];
        $to = 'potcheunam@gmail.com';

        $url = baseUri."linkCode?code=".base64_encode($this->_code)."&email=".base64_encode($this->_datPerson[email]);

        $subject = "DEV-Validación de Email";
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
     * revisamos que se encuentre el user
     */
    private function existsUserInDB(){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(PersonDB_L)->findOneBy(array('user' => $this->_user));
    }

    /**
     * prepara los datos para la alta del usuario
     */
    private function valPassLMS(){
        $LMS = new LMS();

        $apiUser = $LMS->getDataXUserPass($this->_user, $this->_pass, 'https://www.sistemauno.com/source/ws/uno_wsj_login.php');

        $datUser = json_decode($apiUser);
        if($datUser->person->personId){
            //update pass Local
            $pass = Mcrypt::encrypt($this->_pass);

            $em = $this->getDoctrine()->getManager();
            $personDB = $em->getRepository(PersonDB_L)->findOneBy(array('user' => $this->_user));
            if($personDB){
                $personDB->setPassword($pass);
                $em->flush();
            }

            return true;
        }else{
            return false;
        }

    }

    /**
     * @return bool
     * $this->decrypt($encrypt);
     * exit();
     * revisa que se encuentre el user y pass
     */
    private function existsUserPassInDB(){
        $encrypt = Mcrypt::encrypt($this->_pass);
        $em = $this->getDoctrine()->getManager();
        $this->_personDB = $em->getRepository(PersonDB_L)->findOneBy(array('user' => $this->_user, 'password' => $encrypt));
        if($this->_personDB){
            $this->_personDB->setLastLogin(new \DateTime());
            $em->flush();
        }

    }

    /**
     * @return string
     * obtiene los privilegios del usuario logeado y lo envia como json
     */
    private function getPrivilege(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('O.nameOptionApplication, O.ruteOptionApplication, O.iconOptionApplication')
            ->from(PersonDB_L, 'P')
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
        return json_encode($q);
    }

    /**
     * @return string
     * obtiene los perfiles del usuario logeado y lo envia como json
     */
    private function getProfile(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('P2.profileid, P2.profilecode, P2.profile, P1.schoollevelid, SL.schoollevel')
            ->from(PersonDB_L, 'P')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','P1','WITH', 'P.personid = P1.personid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','P2','WITH', 'P1.profileid = P2.profileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','SL','WITH', 'P1.schoollevelid = SL.schoollevelid')
            ->where('P.personid = :personId')
            ->setParameter('personId', $this->_personDB->getPersonid())
            ->groupBy('P2.profileid')
            ->getQuery()
            ->getResult();
        return json_encode($q);
    }

    /**
     * @return string
     * obtiene el Id de la escuela del usuario logeado y lo envia como json
     */
    private function getSchoolId(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('P.schoolid, S.school')
            ->from('UNOEvaluacionesBundle:Personschool', 'P')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH', 'P.schoolid = S.schoolid')
            ->where('P.personid = :personId')
            ->setParameter('personId', $this->_personDB->getPersonid())
            ->groupBy('P.schoolid')
            ->getQuery()
            ->getResult();
        return json_encode($q);
    }

    /**
     * @return string
     * obtiene del sistema y lo envia como json
     */
    private function getVersion(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('V.version, V.releaseDate, F.title, F.description')
            ->from('UNOEvaluacionesBundle:Cversion', 'V')
            ->innerJoin('UNOEvaluacionesBundle:Feature','F','WITH', 'V.idCversion = F.idVersion')
            ->orderBy('V.version', 'DESC')
            ->addOrderBy('F.title', 'ASC')
            ->getQuery()
            ->getResult();

        $v = array();
        $json = array();
        $versions = array_unique(array_column($q,'version'));

        foreach($versions as $val){
            $subQuery = array_filter($q, function($ar) use($val){
                return ($ar['version'] == $val);
            });

            $nuevo = array();
            foreach($subQuery as $value){
                $v['version'] = $value['version'];
                $v['releaseDate'] = $value['releaseDate'];

                array_push( $nuevo, array('title' => $value['title'], 'description' => $value['description']) );
            }
            $v['nuevo'] = $nuevo;
            array_push($json, $v);
        }

        return json_encode($json);
    }

    /**
     * @return bool
     * inicializa las variables de session e invoca a metodo setCookie
     */
    private function logIn(){
        $session = $this->_request->getSession();
        $session->start();
        // set and get session attributes
        $session->set(logged_in, true);
        $session->set(success, false);
        $session->set('personIdS', $this->_personDB->getPersonid());
        $session->set('nameS', $this->_personDB->getName());
        $session->set('privilegeS', $this->getPrivilege());
        $session->set('profileS', $this->getProfile());
        $session->set('schoolIdS', $this->getSchoolId());
        $session->set('versionS', $this->getVersion());
        $session->set('mailing', $this->_personDB->getMailing());
        $session->set('tourEnabled', $this->_personDB->getTourenabled());
        $session->set('assignedS', $this->_personDB->getAssigned());
        $this->setCookie();
        $this->setSurveys($this->_personDB->getPersonid(),$session);
        return true;
    }

    private function setSurveys($pid, $session){

        $response = json_decode(file_get_contents($this->generateUrl('APISurveysPerson',array('personid'=>$pid),true), false), true);

        if(isset ($response['Error'])){

            $session->set('authorized_in',base64_encode(json_encode(array())));
        }

        $session->set('authorized_in',base64_encode(json_encode(array_column($response,'id'))));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * cierra y borra las variables de session
     */
    public function logoutAction(Request $request) {
        $session = $request->getSession();
        $session->clear();
        $session->remove(logged_in);
        $session->remove(success);
        $session->remove('personIdS');
        $session->remove('nameS');
        $session->remove('authorized_in');
        $session->remove('compliance');
        $session->remove('tourEnabled');
        $session->invalidate();
        unset($session);

        return $this->redirect('/');
    }

    /**
     * guarda la cookie del usuario
     */
    private function setCookie() {
        $response = new Response();
        $cookie = new Cookie(login_user, $this->_user, time() + 3600 * 24, '/', 'unoi.com', false, false);
        $response->headers->setCookie($cookie);
        $response->send();
    }

    /**
     * @return bool
     * valida que se pase el user por post
     * en caso contrario lo toma de la cookie
     */
    private function getCookie() {
        $request = $this->get('request');
        $cookies = $request->cookies;

        if ($cookies->has(login_user)) {
            return $cookies->get(login_user);
        }else{
            return false;
        }
    }

    /**
     * @param $api
     * @return bool
     * revisa que la variable retornda sea un objeto
     */
    private function isObjectAPI($api){
        $dat = json_decode($api);
        return is_object($dat);
    }

    /**
     * busca que el mail que tiene el usuario no lo tenga otro usuario
     */
    public function validUniqueMail() {
        $em = $this->getDoctrine()->getManager();
        $Person = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('email' => $this->_datPerson[email]));
        if ($Person) {
            #si existe el mail, por lo que hay q pedirle q ingrece otro
            return false;
        }else{
            return true;
        }

    }

    /**
     * Guarda en la DB la informacion para validar el correo del usuario
     */
    private function validateEmailUser(){
        $this->deleteDuplicateCode($this->_datPerson[personId]);
        $this->_code = rand(10000, 99999);
        $em = $this->getDoctrine()->getManager();
        $UserValidationEmail = new Uservalidationemail();
        $UserValidationEmail->setEmail($this->_datPerson[email]);
        $UserValidationEmail->setCode($this->_code);
        $UserValidationEmail->setDateregister(new \DateTime());
        $UserValidationEmail->setData(json_encode($this->_datPerson));
        $UserValidationEmail->setPersonid($this->_datPerson[personId]);

        $em->persist($UserValidationEmail);
        $em->flush();
    }

    /**
     * @param $personId
     * Borra de la tabla de validacion el registro antiguo
     */
    public function deleteDuplicateCode($personId) {
        $em = $this->getDoctrine()->getManager();
        $UserValidationEmail = $em->getRepository('UNOEvaluacionesBundle:Uservalidationemail')->findOneBy(array('personid' => $personId));
        if ($UserValidationEmail) {
            $em->remove($UserValidationEmail);
            $em->flush();
        }
    }

    /**
     * @param $personId
     * @param $email
     * actualiza el correo de validacion en la BD
     */
    private function updateMailValidation($personId, $email){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb ->update('UNOEvaluacionesBundle:Uservalidationemail', 'u')
            ->set('u.email', $qb->expr()->literal($email))
            ->where('u.personid = ?1')
            ->setParameter(1, $personId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return bool
     * inserta nueva UserHttpSession
     */
    private function addUserHttpSession(){
        $dataBrowser = new Browser();
        $session = $this->_request->getSession();
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
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    /**
     * @Route("/getPass/{personId}")
     *
     * Muestra el formulario de Login
     */
    public function passUserAction(Request $request, $personId){
        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            if (in_array('SuperAdmin', $this->setProfile($session))) {
                $em = $this->getDoctrine()->getManager();
                $Person = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid' => $personId));
                if (!empty($Person)) {
                    return new Response(Mcrypt::decrypt($Person->getPassword()));
                }
            }else{
                throw $this->createNotFoundException('Not Found');
            }
        }
    }

    /**
     * @Route("/encryptPass/{pass}")
     *
     */
    public function encryptPassUserAction(Request $request, $pass){
        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            if (in_array('SuperAdmin', $this->setProfile($session))) {
                if (!empty($pass)) {
                    return new Response(Mcrypt::encrypt($pass));
                }
            }else{
                throw $this->createNotFoundException('Not Found');
            }
        }
    }

    private function setProfile($session){
        $_profile = array();
        $profileJson = json_decode($session->get('profileS'));
        foreach($profileJson as $value){
            array_push($_profile, $value->profile);
        }
        return $_profile;
    }


    /**
     * @Route("/getListUser")
     *
     * Muestra el formulario de Login
     */
    public function getListUserAction(Request $request){

        $session = $request->getSession();
        $session->start();
        if (!$session->get('logged_in')) {

            throw new AccessDeniedHttpException('Necesitas iniciar sesión');
        }

        if (!in_array('SuperAdmin', $this->setProfile($session))) {

            throw $this->createNotFoundException('Not Found');


        }
        $listUser = array();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_person = $qb
            ->select("P.user, P.password")
            ->from('UNOEvaluacionesBundle:Person ','P')
            ->getQuery()
            ->getResult();

        if (empty($_person)) {

            throw $this->createNotFoundException('Not Found');
        }

        foreach($_person as $value){
            array_push($listUser,array( 'user' => $value['user'],'password' => utf8_encode(Mcrypt::decrypt($value['password']))) );
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($listUser);

        return $response;

    }
}