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
    private $_schoolId;
    private $_surveyId;
    private $_personId;
    private $_schoolIdFrm;
    private $_schoolIdPerson;

    private $_surveyResultsGral = array();
    private $_jsonTotalResponsePie = '';
    private $_jsonTotalResponseColumn = '';
    private $_userList = array();
    private $_nameSchool = 'General';

    private $_surveyIdFrm;
    private $_nameSurvey = 'Todas las Evaluaciones';
    private $_andSurvey = "S.surveyid != ''";

    /**
     * @Route("/estadistica")
     *
     * Muestra las estadisticas
     */
    public function indexAction(Request $request){

        $session = $request->getSession();
        $session->start();
        $baseUrl = "http://dev.evaluaciones.unoi.com".$this->container->get('router')->getContext()->getBaseUrl();

        //valida usuario con session iniciada
        if ($session->get('logged_in')) {
            print_r( $this->setProfile($session) );
            //perfiles permitidos
            if (array_intersect(array('SuperAdmin','Director','COACH'), $this->_profile)) {
                if (array_intersect(array('SuperAdmin','COACH'), $this->_profile)) {
                    #vista para SuperAdmin y COACH
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
                    return $this->render('UNOEvaluacionesBundle:Stats:stats.html.twig');
                }
            }else {
                return $this->redirect("/inicio");
            }
        }else{
            return $this->redirect("/");
        }
    }

    /**
     * @param $session
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setProfile($session){
        $profileJson = json_decode($session->get('profileS'));
        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    public function getAPI($service_url){
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        return($curl_response);
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
}