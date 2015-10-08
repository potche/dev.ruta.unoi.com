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
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Controller\Login\Encrypt;
use UNO\EvaluacionesBundle\Entity\Person;
use UNO\EvaluacionesBundle\Entity\School;
use UNO\EvaluacionesBundle\Entity\Schoolperiod;
use UNO\EvaluacionesBundle\Entity\Schoollevel;
use UNO\EvaluacionesBundle\Entity\Profile;
use UNO\EvaluacionesBundle\Entity\Personschool;

class AltaController extends Controller{

    private $_datPersonDB;
    private $_datPerson;
    private $_code;
    private $_email;
    private $_arrayInsert;

    public function linkCodeAction(Request $request) {
        if ($request->getMethod() == 'GET') {
            $session = $request->getSession();
            $session->start();
            if (!$session->get('logged_in')) {
                $this->_code = base64_decode($request->query->get('code'));
                $this->_email = base64_decode($request->query->get('email'));

                if($this->getDatAlta()){
                    if($this->run()){
                        $this->logIn($request);
                        return $this->redirect("/listar");
                    }else{
                        return $this->redirect("/");
                    }
                }else{
                    return $this->render('UNOEvaluacionesBundle:Login:errorCode.html.twig');
                }
            }else{
                return $this->redirect("/listar");
            }

        }else{
            return $this->render('UNOEvaluacionesBundle:Login:errorCode.html.twig');
        }
    }

    private function logIn($request){
        $session = $request->getSession();
        $session->start();
        // set and get session attributes
        $session->set('logged_in', true);
        $session->set('personIdS', $this->_datPerson->personId);
        $session->set('nameS', $this->_datPerson->name);
        $this->setCookie();
        return true;
    }

    private function setCookie() {
        $response = new Response();
        $cookie = new Cookie('login-user', $this->_datPerson->user, time() + 3600 * 24, '/', 'unoi.com', false, false);
        $response->headers->setCookie($cookie);
        $response->send();
    }

    private function run(){
        if ($this->_datPersonDB) {
            $this->_datPerson = json_decode($this->_datPersonDB->getData());
            if($this->setPerson()){
                $this->setSchools();
            }else{
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    private function getDatAlta(){
        $em = $this->getDoctrine()->getManager();
        $this->_datPersonDB = $em->getRepository('UNOEvaluacionesBundle:Uservalidationemail')->findOneBy(array('code' => $this->_code, 'email' => $this->_email));

        if(empty($this->_datPersonDB)){
            return false;
        }else{
            return true;
        }
    }

    private function setPerson(){
        if(!$this->valPerson()){
            $this->addPerson();
            return true;
        }else{
            return false;
        }
    }

    private function valPerson(){
        $em = $this->getDoctrine()->getManager();
        $personDB = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid' => $this->_datPerson->personId));

        if ($personDB) {
            return true;
        }else{
            return false;
        }
    }

    private function addPerson(){
        //inserta nueva person

        $encrypt = encrypt::encrypt($this->_datPerson->pass);
        $em = $this->getDoctrine()->getManager();
        try{
            $Person = new Person();
            $Person->setPersonid($this->_datPerson->personId);
            $Person->setUser($this->_datPerson->user);
            $Person->setPassword($encrypt);
            $Person->setName($this->_datPerson->name);
            $Person->setSurname($this->_datPerson->surname);
            $Person->setGender($this->_datPerson->gender);
            $Person->setBirthday($this->_datPerson->birthDay);
            $Person->setBirthmonth($this->_datPerson->birthMonth);
            $Person->setBirthyear($this->_datPerson->birthYear);
            $Person->setEmail(trim($this->_datPerson->email,'--'));
            $Person->setLanguageid($this->_datPerson->languageId);
            $Person->setLanguageCode($this->_datPerson->languageCode);
            $Person->setLanguage($this->_datPerson->language);
            $Person->setTimezone($this->_datPerson->timeZone);
            $Person->setAdmin(0);
            $Person->setActive(1);

            $em->persist($Person);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            //print_r($e->getMessage());
            return false;
        }
    }

    private function setSchools(){
        $this->_arrayInsert = array();
        $this->_arrayInsert['personId'] = $this->_datPerson->personId;
        foreach( $this->_datPerson->schools as $school ){
            $this->_arrayInsert['schoolId'] = $school->schoolId;
            if(!$this->valSchool($school->schoolId)){
                $this->addSchool($school);
            }
            $this->setSchoolPeriods($school->schoolPeriods);
        }
    }

    private function valSchool($schoolId){
        $em = $this->getDoctrine()->getManager();
        $schoolDB = $em->getRepository('UNOEvaluacionesBundle:School')->findOneBy(array('schoolid' => $schoolId));
        if ($schoolDB) {
            return true;
        }else{
            return false;
        }
    }

    private function addSchool($school){
        //inserta nueva scuela
        $em = $this->getDoctrine()->getManager();

        try{
            $School = new School();
            $School->setSchoolid($school->schoolId);
            $School->setSchoolcode($school->schoolCode);
            $School->setSchool($school->school);
            $School->setCountryid($school->countryId);
            $School->setCountrycode($school->countryCode);
            $School->setCountry($school->country);
            $School->setLanguageid($school->languageId);
            $School->setLanguagecode($school->languageCode);
            $School->setLanguage($school->language);
            $School->setTimezone($school->timeZone);

            $em->persist($School);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            return false;
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            //print_r($e->getMessage());
        }
    }

    private function setSchoolPeriods($schoolPeriods){
        foreach( $schoolPeriods as $schoolPeriod ){
            $this->_arrayInsert['schoolPeriodId'] = $schoolPeriod->schoolPeriodId;
            if(!$this->valSchoolPeriod($schoolPeriod->schoolPeriodId)){
                $this->addSchoolPeriod($schoolPeriod);
            }
            $this->setSchoolLevels($schoolPeriod->schoolLevels);
        }
    }

    private function valSchoolPeriod($schoolPeriodId){
        $em = $this->getDoctrine()->getManager();
        $schoolPeriodDB = $em->getRepository('UNOEvaluacionesBundle:Schoolperiod')->findOneBy(array('schoolperiodid' => $schoolPeriodId));
        if ($schoolPeriodDB) {
            return true;
        }else{
            return false;
        }

    }

    private function addSchoolPeriod($schoolPeriod){
        //inserta nueva scuela
        $em = $this->getDoctrine()->getManager();

        try{
            $Schoolperiod = new Schoolperiod();
            $Schoolperiod->setSchoolperiodid($schoolPeriod->schoolPeriodId);
            $Schoolperiod->setSchoolperiodcode($schoolPeriod->schoolPeriodCode);
            $Schoolperiod->setSchoolperiod($schoolPeriod->schoolPeriod);
            $Schoolperiod->setSchoolperiodactive($schoolPeriod->schoolPeriodActive);

            $em->persist($Schoolperiod);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            return false;
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            //print_r($e->getMessage());
        }
    }

    //---------SchoolLevels
    private function setSchoolLevels($schoolLevels){
        foreach( $schoolLevels as $schoolLevel ){
            $this->_arrayInsert['schoolLevelId'] = $schoolLevel->schoolLevelId;
            if(!$this->valSchoolLevel($schoolLevel->schoolLevelId)){
                $this->addSchoolLevel($schoolLevel);
            }
            $this->setProfiles($schoolLevel->profiles);
        }
    }

    private function valSchoolLevel($schoolLevelId){
        $em = $this->getDoctrine()->getManager();
        $schoolLevelDB = $em->getRepository('UNOEvaluacionesBundle:Schoollevel')->findOneBy(array('schoollevelid' => $schoolLevelId));
        if ($schoolLevelDB) {
            return true;
        }else{
            return false;
        }

    }

    private function addSchoolLevel($schoolLevel){
        //inserta nueva scuela
        $em = $this->getDoctrine()->getManager();

        try{
            $Schoollevel = new Schoollevel();
            $Schoollevel->setSchoollevelid($schoolLevel->schoolLevelId);
            $Schoollevel->setSchoollevelcode($schoolLevel->schoolLevelCode);
            $Schoollevel->setSchoollevel($schoolLevel->schoolLevel);
            $Schoollevel->setSchoollevelcompanyid($schoolLevel->schoolLevelCompanyId);
            $Schoollevel->setColegioNivelCicloId($schoolLevel->colegio_nivel_ciclo_id);

            $em->persist($Schoollevel);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            return false;
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            //print_r($e->getMessage());
        }
    }

    //----------Profiles
    private function setProfiles($profiles){
        foreach( $profiles as $profile ){
            $this->_arrayInsert['profileId'] = $profile->profileId;
            $profile->profileId;
            if(!$this->valProfile($profile->profileId)){
                $this->addProfile($profile);
            }
            $this->insertPermission();
        }

    }

    private function valProfile($profileId){
        $em = $this->getDoctrine()->getManager();
        $profileDB = $em->getRepository('UNOEvaluacionesBundle:Profile')->findOneBy(array('profileid' => $profileId));
        if ($profileDB) {
            return true;
        }else{
            return false;
        }
    }

    private function addProfile($profile){
        //inserta nueva scuela
        $em = $this->getDoctrine()->getManager();

        try{
            $Profile = new Profile();
            $Profile->setProfileid($profile->profileId);
            $Profile->setProfilecode($profile->profileCode);
            $Profile->setProfile($profile->profile);

            $em->persist($Profile);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            return false;
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            //print_r($e->getMessage());
        }
    }

    private function insertPermission(){
        $em = $this->getDoctrine()->getManager();
        try{
            $Personschool = new Personschool();

            $Personschool->setPersonid($this->_arrayInsert['personId']);
            $Personschool->setSchoolid($this->_arrayInsert['schoolId']);
            $Personschool->setSchoolperiodid($this->_arrayInsert['schoolPeriodId']);
            $Personschool->setSchoollevelid($this->_arrayInsert['schoolLevelId']);
            $Personschool->setProfileid($this->_arrayInsert['profileId']);
            $em->persist($Personschool);
            $em->flush();
            //$em->getConnection()->commit();
            return true;
        } catch(\Exception $e){
            return false;
            //print_r($e->getPrevious()->getCode());
            //echo "<br/>----------";
            //print_r($e->getMessage());
        }
    }
}