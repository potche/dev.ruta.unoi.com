<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 16/10/15
 * Time: 10:28 AM
 * Class Login
 * @package UNO\EvaluacionesBundle\Controller\Login
 */


namespace UNO\EvaluacionesBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

/**
 * Class StatsWithAjaxController
 * @package UNO\EvaluacionesBundle\Controller\Stats
 */
class StatsWithAjaxController extends Controller{

    private $_profile = array();
    private $_levelId = 0;
    private $_i = 0;
    private $_schoolLevel = array();
    private $_schoolIdPerson = '';
    private $_nameSchool = 'General';

    /**
     * @Route("/estadisticas")
     *
     * Muestra las estadisticas
     */
    public function indexAction(Request $request){

        $session = $request->getSession();
        $session->start();
        $baseUrl = "http://dev.ruta.unoi.com".$this->container->get('router')->getContext()->getBaseUrl();

        //valida usuario con session iniciada
        if ($session->get('logged_in')) {
            $this->setProfile($session);
            //perfiles permitidos
            if (array_intersect(array('SuperAdmin','Director','COACH'), $this->_profile)) {
                if (array_intersect(array('SuperAdmin','COACH'), $this->_profile)) {
                    #vista para SuperAdmin y COACH
                    //echo "$baseUrl/api/v0/catalog/schools";
                    $schoolListAPI = $this->getAPI("$baseUrl/api/v0/catalog/schools");
                    $schoolList = $this->createSchoolList($schoolListAPI);

                    $surveysListAPI = $this->getAPI("$baseUrl/api/v0/catalog/surveys");
                    $surveyList = $this->createSurveyList($surveysListAPI);

                    return $this->render('UNOEvaluacionesBundle:Stats:stats.html.twig',
                        array(
                            'schoolList' => $schoolList,
                            'surveyList' => $surveyList
                        )
                    );
                }else {
                    #vista para Director
                    $this->setSchooIdPerson($session);
                    if($this->_i === 1 && $this->_levelId !== 0) {
                        $surveysListAPI = $this->getAPI("$baseUrl/api/v0/catalog/survey/school/" . $this->_schoolIdPerson . "/level/" . $this->_levelId);
                    }else{
                        $surveysListAPI = $this->getAPI("$baseUrl/api/v0/catalog/surveys");
                    }
                    $surveyList = $this->createSurveyList($surveysListAPI);

                    return $this->render('UNOEvaluacionesBundle:Stats:statsDir.html.twig',
                        array(
                            'nameSchool' => $this->_nameSchool,
                            'surveyList' => $surveyList,
                            'schoolLevel' => $this->_schoolLevel
                        )
                    );
                }
            }else {
                return $this->redirect("/inicio");
            }
        }else{

            return $this->redirectToRoute('login',array(
                'redirect' => 'estadisticas',
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
        $_levelId = array();
        $_schoolLevel = array();
        $profileJson = json_decode($session->get('profileS'));
        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
            array_push($_levelId, $value->schoollevelid);
            array_push($_schoolLevel, $value->schoollevel);
        }
        $_schoolLevel = array_unique($_schoolLevel);
        $this->_schoolLevel = implode(', ',$_schoolLevel);

        $_levelId = array_unique($_levelId);
        if(sizeof($_levelId) === 1){
            //el usuario solo tiene un nivel, por lo que solo se le mostrara las listas de su nivel
            $this->_levelId = $_levelId[0];
        }else{
            //el usuario tiene multiples niveles, por lo que se le mostrara todas las lista
            $this->_schoolLevel = rtrim($this->_schoolLevel, ', ');
        }
    }

    public function getAPI($service_url){
        
        /*$curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        curl_close($curl);

        var_dump('url: '.$service_url);
        var_dump('respuesta: '.$curl_response);
        return($curl_response);*/

        return file_get_contents($service_url, false);

    }

    private function createSchoolList($schoolList){
        $arraySchool = array();
        foreach(json_decode($schoolList) as $value){
            array_push( $arraySchool, $value->schoolid .'-'. $value->school );
        }

        return json_encode($arraySchool);
    }

    private function createSurveyList($surveyList){

        $arraySurvey = array();
        foreach(json_decode($surveyList) as $value){
            array_push( $arraySurvey, $value->surveyid .'-'. $value->title );
        }

        return json_encode($arraySurvey);
    }

    /**
     * @param $session
     *
     * filtra la escuela que puede ver el usuario
     */
    private function setSchooIdPerson($session){
        $this->_i = 0;
        $schoolIdPerson = '';
        $_schoolIdPerson = json_decode($session->get('schoolIdS'));
        if (!array_intersect(array('SuperAdmin','COACH'), $this->_profile)) {
            foreach($_schoolIdPerson as $value){
                $this->_i++;
                $schoolIdPerson .= $value->schoolid. ', ';
                $this->_nameSchool = $value->schoolid.'-'.$value->school;
            }
        }

        $this->_schoolIdPerson = rtrim($schoolIdPerson, ', ');
    }
}