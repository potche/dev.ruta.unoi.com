<?php

namespace UNO\EvaluacionesBundle\Controller\API_SimuladorCostos;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\API_SimuladorCostos\APIUtils;
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Controller\Login\Mcrypt;
use UNO\EvaluacionesBundle\Entity\Person;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 8/11/16
 * Time: 4:40 PM
 */

/**
 * @Route("/api/v0/simCosts/Authentication")
 *
 */
class AuthenticationController extends Controller{

    private $_url = 'https://www.sistemauno.com/source/ws/uno_wsj_login.php';

    private $_user;
    private $_password;

    private $_datLMS;
    private $_datDB;

    private $_results = array();
    private $_status;
    private $_message;

    /**
     * @Route("")
     *
     */
    public function indexAction(Request $request){

        $APIUtils = new APIUtils();

        if ($request->getMethod() == 'POST') {

            $this->_user = $request->request->get('user');
            $this->_password = $request->request->get('password');

            $this->run();

        }else{

            $this->_status = 405;

        }

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);
    }

    private function run(){


        if (!empty($this->_user) && !empty($this->_password)) {

            $this->validate();

        } else if (!empty($this->_password)){

            $this->_status = 400;
            $this->_message = 'parametro user vacio';

        } else if (!empty($this->_user)){

            $this->_status = 400;
            $this->_message = 'parametro password vacio';

        }else {

            $this->_status = 400;
            $this->_message = 'parametros invalidos';

        }
    }

    private function validate(){

        if($this->validateLMS() && $this->validateDB()) {

            $this->_status = 200;
            $this->_results = array(
                'personId' => $this->_datLMS->person->personId,
                'name' => $this->_datLMS->person->name,
                'surname' => $this->_datLMS->person->surname
            );

        }else if($this->_datLMS && $this->existsPersonIdInDB()){

            $this->_status = 200;
            $this->_results = array(
                'personId' => $this->_datLMS->person->personId,
                'name' => $this->_datLMS->person->name,
                'surname' => $this->_datLMS->person->surname
            );

        }else {

            $this->_status = 200;
            $this->_message = 'Autenticación Invalida, intente de nuevo';

        }

    }

    private function validateLMS(){

        $ok = true;
        $LMS = new LMS();
        $api = $LMS->getDataXUserPass($this->_user, $this->_password, $this->_url);

        if (!$this->isObjectAPI($api)) {
            $ok = false;
        }

        return $ok;
    }

    /**
     * @return bool
     * $this->decrypt($encrypt);
     * exit();
     * revisa que se encuentre el user y pass
     */
    private function validateDB(){

        $ok = true;
        $em = $this->getDoctrine()->getManager();
        $this->_datDB = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('user' => $this->_user, 'password' => Mcrypt::encrypt($this->_password)));

        if($this->_datDB) {
            $this->_datDB->setLastLogin(new \DateTime());
            $em->flush();
        }else{
            //el usuario y password no se encuentran en la BD
            $ok = false;
        }

        return $ok;
    }

    /**
     * @return bool
     * revisamos que se encuentre el personId en la base de datos
     */
    private function existsPersonIdInDB(){

        $ok = true;
        if(is_object($this->_datLMS)) {

            $em = $this->getDoctrine()->getManager();
            $Person = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid' => $this->_datLMS->person->personId));

            if ($Person) {
                $Person->setUser($this->_user);
                $Person->setPassword(Mcrypt::encrypt($this->_password));
                $em->flush();
            } else {

                if ($this->_datLMS && !$this->addPerson()) {
                    //no se encuentran en la BD
                    $ok = false;
                }

            }

        }else{
            $ok = false;
        }

        return $ok;

    }

    /**
     * @return bool
     * inserta nueva person
     */
    private function addPerson(){

        $em = $this->getDoctrine()->getManager();
        
        try{
            $Person = new Person();
            $Person->setPersonid($this->_datLMS->person->personId);
            $Person->setUser($this->_datLMS->person->user);
            $Person->setPassword(Mcrypt::encrypt($this->_password));
            $Person->setName($this->_datLMS->person->name);
            $Person->setSurname($this->_datLMS->person->surname);
            $Person->setGender('M');
            $Person->setBirthday($this->_datLMS->person->birthDay);
            $Person->setBirthmonth($this->_datLMS->person->birthMonth);
            $Person->setBirthyear($this->_datLMS->person->birthYear);
            $Person->setEmail($this->_datLMS->person->email);
            $Person->setLanguageid($this->_datLMS->person->languageId);
            $Person->setLanguageCode($this->_datLMS->person->languageCode);
            $Person->setLanguage($this->_datLMS->person->language);
            $Person->setTimezone($this->_datLMS->person->timeZone);
            $Person->setAdmin(0);
            $Person->setActive(1);
            $Person->setMailing(1);
            $Person->setLastLogin(new \DateTime());

            $em->persist($Person);
            $em->flush();
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }


    /**
     * @param $api
     * @return bool
     * revisa que la variable retornda sea un objeto
     */
    private function isObjectAPI($api){

        $this->_datLMS = json_decode($api);
        return is_object($this->_datLMS);

    }

}