<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Controller\LMS\FilterAPI;
use UNO\EvaluacionesBundle\Controller\Login\Encrypt;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Ccoach;


use UNO\EvaluacionesBundle\Controller\API\APIUtils;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 11/04/16
 * Time: 13:05 AM
 */

/**
 * @Route("/api/v0/profile")
 *
 */
class APIProfileController extends Controller{
    private $_user;
    private $_pass;
    private $_id;
    private $_datPerson;
    private $_arrayInsert = array();

    /**
     * @Route("/{id}")
     * @Method({"GET"})
     *
     */
    public function profileAction($id){

        if (preg_match('/^[0-9]+$/', $id)) {

            $this->_id = $id;
            if($this->getUserPass()){
                $this->getProfileLMS();
            }else{
                $result = "boooo";
            }

            #-----envia la respuesta en JSON-----#
            $response = new JsonResponse();
            $response->setData($this->_arrayInsert);

            return $response;
        }else{
            #-----envia la respuesta en JSON-----#
            $responseError = new JsonResponse();
            $responseError->setData(['status'=> 'error', 'message' => 'day is not a valid format']);
            return $responseError;
        }
    }

    /**
     * $this->decrypt($encrypt);
     * exit();
     * obtine user y pass
     */
    private function getUserPass(){
        $em = $this->getDoctrine()->getManager();
        $_personDB = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid' => $this->_id));
        if($_personDB){
            $this->_user = $_personDB->getUser();
            $this->_pass = encrypt::decrypt($_personDB->getPassword());
            return true;
        }else{
            return false;
        }

    }

    private function getProfileLMS(){
        $LMS = new LMS();
        $apiUser = $LMS->getDataXUserPass($this->_user, $this->_pass, 'https://www.sistemauno.com/source/ws/uno_wsj_login.php');

        //validar Permisos
        $FilterAPI = new FilterAPI($apiUser);
        $this->_datPerson = $FilterAPI->runFilter($this->_user, $this->_pass);
        //borra los perfile de personSchool
        $this->deleteProfile();
        //arma e inserta elos perfiles en personSchool
        $this->setSchools();
    }

    /**
     * @return bool
     */
    private function deleteProfile(){
        $em = $this->getDoctrine()->getManager();
        $_personSchools = $em->getRepository('UNOEvaluacionesBundle:Personschool')->findBy(array('personid' => $this->_id));
        foreach ($_personSchools as $personSchool) {
            $em->remove($personSchool);
        }
        $em->flush();
    }

    /**
     * guarda las escuelas en un array que servira
     * para insertarlas en la base de datos
     */
    private function setSchools(){
        $this->_arrayInsert = array();
        $this->_arrayInsert['personId'] = $this->_id;
        foreach( $this->_datPerson['schools'] as $school ){
            $this->_arrayInsert['schoolId'] = $school['schoolId'];

            $this->setSchoolPeriods($school['schoolPeriods']);
        }
    }

    /**
     * @param $schoolPeriods
     */
    private function setSchoolPeriods($schoolPeriods){
        foreach( $schoolPeriods as $schoolPeriod ){
            $this->_arrayInsert['schoolPeriodId'] = $schoolPeriod['schoolPeriodId'];

            $this->setSchoolLevels($schoolPeriod['schoolLevels']);
        }
    }

    /**
     * @param $schoolLevels
     * prepara los niveles que no se encuentran en la DB para insertarlos
     */
    private function setSchoolLevels($schoolLevels){
        foreach( $schoolLevels as $schoolLevel ){
            $this->_arrayInsert['schoolLevelId'] = $schoolLevel['schoolLevelId'];

            $this->setProfiles($schoolLevel['profiles']);
        }
    }

    /**
     * @param $profiles
     */
    private function setProfiles($profiles){
        foreach( $profiles as $profile ){
            $this->_arrayInsert['profileId'] = $profile['profileId'];

            $this->insertPermission();
        }
    }

    /**
     * @return bool
     */
    private function insertPermission(){

        $em = $this->getDoctrine()->getManager();
        try{
            $Personschool = new Personschool();

            $Personschool->setPersonid($this->_arrayInsert['personId']);
            $Personschool->setSchoolid($this->_arrayInsert['schoolId']);
            $Personschool->setSchoolperiodid($this->_arrayInsert['schoolPeriodId']);
            $Personschool->setSchoollevelid($this->_arrayInsert['schoolLevelId']);
            if($this->valIsCoach()){
                $Personschool->setProfileid(2);
            }else{
                $Personschool->setProfileid($this->_arrayInsert['profileId']);
            }
            $em->persist($Personschool);
            $em->flush();
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    /**
     * @return object
     * valida que el usuario este dado de alta
     */
    private function valIsCoach(){
        $em = $this->getDoctrine()->getManager();
        return($em->getRepository('UNOEvaluacionesBundle:Ccoach')->findOneBy(array('user' => $this->_datPerson['user'])));
    }

}