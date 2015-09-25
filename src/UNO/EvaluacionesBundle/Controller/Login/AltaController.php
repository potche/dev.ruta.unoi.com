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

class AltaController extends Controller{
    /**
     * @var int
     * @access private
     */
    static $status= 1;
    /**
     * @var bool
     * @access static
     */
    static $ok = FALSE;
    /**
     * @var bool
     * @access static
     */
    static $profileG = FALSE;
    /**
     * @var bool
     * @access static
     */
    static $periodoG = FALSE;

    /**
     * @Route("/alta")
     */
    public function indexAction(){
        $dataBrowser = new Browser();

        return $this->render('UNOEvaluacionesBundle:Login:index.html.twig', array(
            'browser' => $dataBrowser->getBrowser(),
            'browserVersion' => $dataBrowser->getVersion()
        ));

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

    public function run(){
        if(empty($this->_failure)){
            $person = $this->getPerson();
            $schools = $this->getSchools();
            if(static::$status == 1){
                echo "<h1>".static::$status."</h1>";
                $data = array();
                $data['person'] = $person;
                $data['schools'] = $schools;
                return $data;
            }  else {
                return static::$status;
            }
        }else{
            return array(
                'success' => false,
                'message' => $this->_failure
            );
        }
    }

    private function getPerson(){

        $persona = array(
            'personaId' => $this->_api['person']['personId'],
            'username' => $this->getUser(),
            'password'=> $this->getPassword(),
            'name' => $this->_api['person']['name'],
            'surname' => $this->_api['person']['surname'],
            'birthDay' => $this->_api['person']['birthDay'],
            'birthMonth' => $this->_api['person']['birthMonth'],
            'birthYear' => $this->_api['person']['birthYear'],
            'email' => $this->_api['person']['email'],
            'languageCode' => $this->_api['person']['languageCode'],
            'language' => $this->_api['person']['language']
        );

        return $persona;
    }

    /**
     * Función que obtiene las escuelas del array obtenido de la respuesta del API LMS
     *
     * @return array
     */
    private function getSchools() {
        $schoolsf = array();
        //schools
        foreach ($this->_api['person']['schools'] as $schools) {
            //school
            foreach ($schools as $school) {
                //data school
                foreach ($school as $value) {
                    if($value['countryCode'] == 'MX'){
                        static::$status = 1;
                        $tmp = array(
                            'schoolId' => $value['schoolId'],
                            'schoolCode' => $value['schoolCode'],
                            'school' => $value['school'],
                            'countryId' => $value['countryId'],
                            'countryCode' => $value['countryCode'],
                            'country' => $value['country'],
                            'languageId' => $value['languageId'],
                            'languageCode' => $value['languageCode'],
                            'language' => $value['language'],
                            'timeZone' => $value['timeZone']
                        );
                        $varios = $this->getSchoolPeriods($value['schoolPeriods'], $tmp);
                        array_push($schoolsf, $varios);
                    }else{
                        static::$status = 103;
                    }
                }
            }
        }

        $personas = array();
        foreach ($schoolsf as $nvl1) {
            foreach ($nvl1 as $nvl2) {
                foreach ($nvl2 as $nvl3) {
                    foreach ($nvl3 as $nvl4) {
                        array_push($personas, $nvl4);
                    }
                }
            }
        }

        return $personas;
    }

    /**
     * Función que obtiene los periodos del array obtenido en la función Login
     *
     * @param $schoolPeriods
     * @param $persona
     *
     * @return array
     */
    private function getSchoolPeriods($schoolPeriods, $persona) {
        $schpl = array();

        foreach ($schoolPeriods as $sp) {
            $periodo = FALSE;
            foreach ($sp['schoolPeriod'] as $value) {
                //solo periodos activos
                $tmp = array(
                    'schoolPeriodId' => $value['schoolPeriodId'],
                    'schoolPeriodCode' => $value['schoolPeriodCode'],
                    'schoolPeriod' => $value['schoolPeriod'],
                    'schoolPeriodActive' => $value['schoolPeriodActive']
                );
                $varios = $this->getSchoolLevel($value['schoolLevels'], array_merge($persona, $tmp));
                array_push($schpl, $varios);
                /*
                if ($value['schoolPeriodActive']) {
                    $periodo = TRUE;
                    static::$status = 1;
                    static::$periodoG = TRUE;
                    $tmp = array(
                        'schoolPeriodId' => $value['schoolPeriodId'],
                        'schoolPeriodCode' => $value['schoolPeriodCode'],
                        'schoolPeriod' => $value['schoolPeriod'],
                        'schoolPeriodActive' => $value['schoolPeriodActive']
                    );
                    $varios = $this->getSchoolLevel($value['schoolLevels'], array_merge($persona, $tmp));
                    array_push($schpl, $varios);
                }else{
                    if(!$periodo  && !static::$periodoG )
                        static::$status = 102;
                }
                */
            }
        }
        return $schpl;
    }

    /**
     * Función que obtiene los niveles del array obtenido en la función Login
     *
     * @param $schoolLevel
     * @param $persona
     *
     * @return array
     */
    private function getSchoolLevel($schoolLevel, $persona) {
        $schpl = array();
        foreach ($schoolLevel as $sp) {
            foreach ($sp['schoolLevel'] as $value) {
                $tmp = array(
                    'schoolLevelId' => $value['schoolLevelId'],
                    'schoolLevelCode' => $value['schoolLevelCode'],
                    'schoolLevel' => $value['schoolLevel'],
                    'schoolLevelCompanyId' => $value['schoolLevelCompanyId'],
                    'colegio_nivel_ciclo_id' => $value['colegio_nivel_ciclo_id']
                );
                $varios = $this->getProfiles($value['profiles'], array_merge($persona, $tmp));
                array_push($schpl, $varios);
            }
        }
        return $schpl;
    }

    /**
     * Función que obtiene los perfiles del array obtenido en la función Login
     *
     * @param $profiles
     * @param $persona
     * @return array
     */
    private function getProfiles($profiles, $persona) {
        $schpl = array();
        foreach ($profiles as $sp) {
            foreach ($sp['profile'] as $value) {
                //id Familiar
                $tmp = array(
                    'profileId' => $value['profileId'],
                    'profileCode' => $value['profileCode'],
                    'profile'=> $value['profile']
                );
                array_push($schpl, array_merge($persona, $tmp));
            }
        }
        return $schpl;
    }
}