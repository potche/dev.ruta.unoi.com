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
    private $_levelId = '';
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

        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseUrl = $this->container->get('router')->getContext()->getBaseUrl();

        $base = $scheme.$host;

        //valida usuario con session iniciada
        if ($session->get('logged_in')) {
            $this->setProfile($session);
            //perfiles permitidos
            if (array_intersect(array('SuperAdmin','Director','COACH'), $this->_profile)) {
                if (array_intersect(array('SuperAdmin','COACH'), $this->_profile)) {
                    #vista para SuperAdmin y COACH
                    $schoolListAPI = file_get_contents("$base/api/v0/catalog/schools", false);
                    $schoolList = $this->createSchoolList($schoolListAPI);

                    $surveysListAPI = file_get_contents("$base/api/v0/catalog/surveys", false);
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
                    $this->setSchoolLevel($session);
                    
                    if($this->_i === 1 ) {
                        $surveysListAPI = $this->surveyList(array('schoolId' => $this->_schoolIdPerson), 'PS.schoolid = :schoolId AND PS.schoollevelid in ('.$this->_levelId.')');
                    }else{
                        $surveysListAPI = file_get_contents("$base/api/v0/catalog/surveys", false);
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
        $profileJson = json_decode($session->get('profileS'));

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    /**
     * @param $session
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setSchoolLevel($session){
        $_levelId = array();
        $_schoolLevel = array();
        $schoolLevelJson = json_decode($session->get('schoolLevelS'));

        foreach($schoolLevelJson as $value){
            array_push($_levelId, $value->schoollevelid);
            array_push($_schoolLevel, $value->schoollevel);
        }
        $_schoolLevel = array_unique($_schoolLevel);
        $_levelId = array_unique($_levelId);

        if(sizeof($_levelId) === 1){
            //el usuario solo tiene un nivel, por lo que solo se le mostrara las listas de su nivel
            $this->_levelId = $_levelId[0];
            $this->_schoolLevel = $_schoolLevel[0];
        }else{
            //el usuario tiene multiples niveles, por lo que se le mostrara todas las lista
            $this->_levelId = implode(', ',$_levelId);
            $this->_schoolLevel = implode(', ',$_schoolLevel);

        }
    }

    public function getAPI($service_url){
        $postdata = http_build_query(
            array(
                'schoolLevelId' => $this->_levelId
            )
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context  = stream_context_create($opts);


        return file_get_contents($service_url, false, $context);

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

    /**
     * @param $parameters
     * @param $where
     * @return mixed
     */
    private function surveyList($parameters, $where){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return json_encode($qb->select("S.surveyid, S.title")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile ','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'S.surveyid = SP.surveySurveyid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere($where)
            ->setParameters($parameters)
            ->groupBy('S.surveyid')
            ->orderBy( 'S.surveyid')
            ->getQuery()
            ->getResult()
        );
    }
}