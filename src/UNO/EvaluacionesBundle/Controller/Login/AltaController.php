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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use UNO\EvaluacionesBundle\Controller\Login\Browser;
use UNO\EvaluacionesBundle\Controller\LMS\LMS;
use UNO\EvaluacionesBundle\Controller\Login\Mcrypt;
use UNO\EvaluacionesBundle\Entity\Person;
use UNO\EvaluacionesBundle\Entity\School;
use UNO\EvaluacionesBundle\Entity\Schoolperiod;
use UNO\EvaluacionesBundle\Entity\Schoollevel;
use UNO\EvaluacionesBundle\Entity\Profile;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Ccoach;

define('PERSONDB', 'UNOEvaluacionesBundle:Person');
define('PROFILEDB', 'UNOEvaluacionesBundle:Profile');
define('PERSONSCHOOLDB','UNOEvaluacionesBundle:Personschool');
/**
 * Class AltaController
 * @package UNO\EvaluacionesBundle\Controller\Login
 */
class AltaController extends Controller{

    /**
     * @var
     */
    private $_datPersonDB;
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
    private $_email;
    /**
     * @var
     */
    private $_arrayInsert;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function linkCodeAction(Request $request) {
        if ($request->getMethod() == 'GET') {
            $session = $request->getSession();
            $session->start();
            if (!$session->get('logged_in')) {
                $this->_code = base64_decode($request->query->get('code'));
                $this->_email = base64_decode($request->query->get('email'));

                $this->getDatAlta();
                if(!empty($this->_datPersonDB)){
                    if($this->run()){
                        $this->logIn($request);
                        $session->set('success', true);
                        return $this->redirect("/success");
                    }else{
                        return $this->redirect("/");
                    }
                }else{
                    return $this->render('UNOEvaluacionesBundle:Login:errorCode.html.twig');
                }
            }else{
                return $this->redirect("/inicio");
            }
        }else{
            return $this->render('UNOEvaluacionesBundle:Login:errorCode.html.twig');
        }
    }

    /**
     * @return string
     * regresa el json con los privilegios
     */
    private function getPrivilege(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('O.nameOptionApplication, O.ruteOptionApplication, O.iconOptionApplication')
            ->from(PERSONDB, 'P')
            ->innerJoin(PERSONSCHOOLDB,'P1','WITH', 'P.personid = P1.personid')
            ->innerJoin(PROFILEDB,'P2','WITH', 'P1.profileid = P2.profileid')
            ->innerJoin('UNOEvaluacionesBundle:Privilege','P3','WITH', 'P2.profileid = P3.profileId')
            ->innerJoin('UNOEvaluacionesBundle:Optionapplication','O','WITH', 'O.optionApplicationId = P3.optionApplicationId')
            ->where('P.personid = :personId')
            ->andWhere('O.statusOptionApplication = 1')
            ->setParameter('personId', $this->_datPerson->personId)
            ->groupBy('O.nameOptionApplication, O.ruteOptionApplication, O.iconOptionApplication')
            ->getQuery()
            ->getResult();

        return json_encode($q);
    }

    /**
     * @return string
     * regresa el json con los profile
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
            ->setParameter('personId', $this->_datPerson->personId)
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
        $q = $qb->select('P.schoolid')
            ->from(PERSONSCHOOLDB, 'P')
            ->where('P.personid = :personId')
            ->setParameter('personId', $this->_datPerson->personId)
            ->groupBy('P.schoolid')
            ->getQuery()
            ->getResult();
        return json_encode($q);
    }

    /**
     * @param $request
     * @return bool
     */
    private function logIn($request){
        $session = $request->getSession();
        $session->start();
        // set and get session attributes
        $session->set('logged_in', true);
        $session->set('personIdS', $this->_datPerson->personId);
        $session->set('nameS', $this->_datPerson->name);
        $session->set('privilegeS', $this->getPrivilege());
        $session->set('profileS', $this->getProfile());
        $session->set('schoolIdS', $this->getSchoolId());
        $session->set('versionS', $this->getVersion());
        $session->set('mailing', 1);
        $session->set('tourEnabled', 1);
        $session->set('assignedS', 0);
        $this->setCookie();
        $this->setSurveys($this->_datPerson->personId,$session);
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
     * crea las Cookies
     */
    private function setCookie() {
        $response = new Response();
        $cookie = new Cookie('login-user', $this->_datPerson->user, time() + 3600 * 24, '/', 'unoi.com', false, false);
        $response->headers->setCookie($cookie);
        $response->send();
    }

    /**
     * @return bool
     */
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

    /**
     * busca si el usuario solicito validar el mail
     */
    private function getDatAlta(){
        $em = $this->getDoctrine()->getManager();
        $this->_datPersonDB = $em->getRepository('UNOEvaluacionesBundle:Uservalidationemail')->findOneBy(array('code' => $this->_code, 'email' => $this->_email));
    }

    /**
     * @return bool
     */
    private function setPerson(){
        if( empty($this->valPerson()) ){
            $this->addPerson();
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return object
     * valida que el usuario este dado de alta
     */
    private function valPerson(){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(PERSONDB)->findOneBy(array('personid' => $this->_datPerson->personId));
    }

    /**
     * @return object
     * valida que el usuario este dado de alta
     */
    private function valIsCoach(){
        $em = $this->getDoctrine()->getManager();
        return($em->getRepository('UNOEvaluacionesBundle:Ccoach')->findOneBy(array('user' => $this->_datPerson->user)));
    }

    /**
     * @return bool
     * inserta nueva person
     */
    private function addPerson(){
        $encrypt = Mcrypt::encrypt($this->_datPerson->pass);
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
            $Person->setEmail($this->_email);
            $Person->setLanguageid($this->_datPerson->languageId);
            $Person->setLanguageCode($this->_datPerson->languageCode);
            $Person->setLanguage($this->_datPerson->language);
            $Person->setTimezone($this->_datPerson->timeZone);
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
     * guarda las escuelas en un array que servira
     * para insertarlas en la base de datos
     */
    private function setSchools(){
        $this->_arrayInsert = array();
        $this->_arrayInsert['personId'] = $this->_datPerson->personId;
        foreach( $this->_datPerson->schools as $school ){
            $this->_arrayInsert['schoolId'] = $school->schoolId;
            if( empty($this->valSchool($school->schoolId)) ){
                $this->addSchool($school);
            }
            $this->setSchoolPeriods($school->schoolPeriods);
        }
    }

    /**
     * @param $schoolId
     * @return bool
     */
    private function valSchool($schoolId){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('UNOEvaluacionesBundle:School')->findOneBy(array('schoolid' => $schoolId));
    }

    /**
     * @param $school
     * @return bool
     * inserta nueva scuela
     */
    private function addSchool($school){
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
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    /**
     * @param $schoolPeriods
     */
    private function setSchoolPeriods($schoolPeriods){
        foreach( $schoolPeriods as $schoolPeriod ){
            $this->_arrayInsert['schoolPeriodId'] = $schoolPeriod->schoolPeriodId;
            if( empty($this->valSchoolPeriod($schoolPeriod->schoolPeriodId)) ){
                $this->addSchoolPeriod($schoolPeriod);
            }
            $this->setSchoolLevels($schoolPeriod->schoolLevels);
        }
    }

    /**
     * @param $schoolPeriodId
     * @return bool
     */
    private function valSchoolPeriod($schoolPeriodId){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('UNOEvaluacionesBundle:Schoolperiod')->findOneBy(array('schoolperiodid' => $schoolPeriodId));
    }

    /**
     * @param $schoolPeriod
     * @return bool
     * inserta nueva scuela
     */
    private function addSchoolPeriod($schoolPeriod){
        $em = $this->getDoctrine()->getManager();

        try{
            $Schoolperiod = new Schoolperiod();
            $Schoolperiod->setSchoolperiodid($schoolPeriod->schoolPeriodId);
            $Schoolperiod->setSchoolperiodcode($schoolPeriod->schoolPeriodCode);
            $Schoolperiod->setSchoolperiod($schoolPeriod->schoolPeriod);
            $Schoolperiod->setSchoolperiodactive($schoolPeriod->schoolPeriodActive);

            $em->persist($Schoolperiod);
            $em->flush();
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    /**
     * @param $schoolLevels
     * prepara los niveles que no se encuentran en la DB para insertarlos
     */
    private function setSchoolLevels($schoolLevels){
        foreach( $schoolLevels as $schoolLevel ){
            $this->_arrayInsert['schoolLevelId'] = $schoolLevel->schoolLevelId;
            if( empty( $this->valSchoolLevel($schoolLevel->schoolLevelId) ) ){
                $this->addSchoolLevel($schoolLevel);
            }
            $this->setProfiles($schoolLevel->profiles);
        }
    }

    /**
     * @param $schoolLevelId
     * @return bool
     */
    private function valSchoolLevel($schoolLevelId){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('UNOEvaluacionesBundle:Schoollevel')->findOneBy(array('schoollevelid' => $schoolLevelId));
    }

    /**
     * @param $schoolLevel
     * @return bool
     * inserta nueva escuela
     */
    private function addSchoolLevel($schoolLevel){
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

            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    /**
     * @param $profiles
     */
    private function setProfiles($profiles){
        foreach( $profiles as $profile ){
            $this->_arrayInsert['profileId'] = $profile->profileId;
            $profile->profileId;
            if( empty($this->valProfile($profile->profileId)) ){
                $this->addProfile($profile);
            }
            $this->insertPermission();
        }
    }

    /**
     * @param $profileId
     * @return bool
     */
    private function valProfile($profileId){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(PROFILEDB)->findOneBy(array('profileid' => $profileId));
    }

    /**
     * @param $profile
     * @return bool
     */
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
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
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
}
