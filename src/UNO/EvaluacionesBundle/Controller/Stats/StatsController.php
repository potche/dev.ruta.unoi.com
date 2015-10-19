<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 16/10/15
 * Time: 10:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

class StatsController extends Controller{

    private $_profile = array();
    private $_schoolId = array();
    private $_jsonTotalResponse;
    private $_personId;
    private $_schoolIdFrm;
    /**
     * @Route("/estadisticas")
     *
     * Muestra las estadisticas
     */
    public function indexAction(Request $request){

        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $this->setProfile($session);
            $this->setPersonId($session);
            $this->setSchoolIdFrm($request);
            $this->getSchoolResponse();
            $this->getResults();
            if( in_array('SuperAdmin', $this->_profile) ){
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas Admin',
                    'jsonTotalResponse' => $this->_jsonTotalResponse,
                    'schoolId' => $this->_schoolId
                ));
            }else{
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas',
                    'dat' => $this->getResults()
                ));
            }

        }else{
            return $this->redirect("/");
        }
    }

    private function setProfile($session){
        $profileJson = json_decode($session->get('profileS'));

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    private function setPersonId($session){
        $this->_personId = $session->get('personIdS');
    }

    private function setSchoolIdFrm($request){
        if(!empty($request->query->get('schoolId'))){
            $this->_schoolIdFrm = $request->query->get('schoolId');
        }else{
            $this->_schoolIdFrm = 0;
        }

    }

    private function getResults() {
        $resultsArray = $this->getSurveyResults();

        if( !empty($resultsArray) ){
            //$this->getSchoolId($resultsArray);
            $this->getTotalResponse($resultsArray);
        }
    }

    private function getSurveyResults() {
        $query = "SELECT P.personid, PS.schoolid, Sc.school, S.title, QS.order, Q.question, Sub.subcategory, A.answer, A.comment
                                  FROM UNOEvaluacionesBundle:Answer A
                                  INNER JOIN UNOEvaluacionesBundle:Optionxquestion OQ WITH A.optionxquestion = OQ.optionxquestionId
                                  INNER JOIN UNOEvaluacionesBundle:Questionxsurvey QS WITH OQ.questionxsurvey = QS.questionxsurveyId
                                  INNER JOIN UNOEvaluacionesBundle:Question Q WITH QS.questionQuestionid = Q.questionid
                                  INNER JOIN UNOEvaluacionesBundle:Subcategory Sub WITH Q.subcategorySubcategoryid = Sub.subcategoryid
                                  INNER JOIN UNOEvaluacionesBundle:Survey S WITH QS.surveySurveyid = S.surveyid
                                  INNER JOIN UNOEvaluacionesBundle:Personschool PS WITH A.personPersonid = PS.personid
                                  INNER JOIN UNOEvaluacionesBundle:Person P WITH PS.personid = P.personid
                                  INNER JOIN UNOEvaluacionesBundle:School Sc WITH PS.schoolid = Sc.schoolid
                                  ";

        $em = $this->getDoctrine()->getManager();
        if( !in_array('SuperAdmin', $this->_profile) ){
            $query .= "WHERE A.personPersonid = ".$this->_personId;

        }else{
            if($this->_schoolIdFrm != 0){
                $query .= "WHERE PS.schoolid = ".$this->_schoolIdFrm;
            }
        }
        $query .= "GROUP BY A.personPersonid, PS.schoolid, S.surveyid, QS.order
                   ORDER BY A.personPersonid, PS.schoolid, S.surveyid, QS.order";
        $q = $em->createQuery($query);
            //->setParameter('price', '19.99');
        $results = $q->getResult();
        return $results;
    }

    private function getSchoolId($resultsArray){
        $schoolId = array();
        $school = array();
        $i = 0;

        foreach($resultsArray as $value){
            array_push($schoolId, $value['schoolid']);
            array_push($school, $value['school']);
        }
        //solo los unicos
        $schoolIdArray = array_unique($schoolId);
        $schoolArray = array_unique($school);
        //le da formato al array final
        foreach($schoolIdArray as $key => $value){
            $this->_schoolId[$i] = array('schoolId'=>$value, 'school' => $schoolArray[$key]);
            $i++;
        }
    }

    private function getTotalResponse($resultsArray){
        $si = 0;
        $no = 0;
        $nose = 0;
        foreach($resultsArray as $value){
            switch ($value['answer']):
                case 'Sí':
                    $si ++;
                    break;
                case 'No':
                    $no ++;
                    break;
                default:
                    $nose ++;
            endswitch;
        }

        $total = $si + $no + $nose;
        /*
         * [{name:'No',y:1,sliced:false,selected:false},{name:'Sí',y:2,sliced:true,selected:true}]
         */
        $this->_jsonTotalResponse =
                "[
                    {
                    name:'Sí',
                    y:".$this->getPorcentaje($total, $si, 2).",
                    sliced:true,
                    selected:true
                    },
                    {
                    name:'No',
                    y:".$this->getPorcentaje($total, $no, 2).",
                    sliced:false,
                    selected:false
                    },
                    {
                    name:'No sé',
                    y:".$this->getPorcentaje($total, $nose, 2).",
                    sliced:false,
                    selected:false
                    }
                ]";
    }

    private function getPorcentaje($total, $parte, $redondear = 2) {
        return round($parte / $total * 100, $redondear);

    }

    private function getSchoolResponse() {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $this->_schoolId = $qb->select('PS.schoolid','Sc.school')
            ->from('UNOEvaluacionesBundle:Answer','A')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'A.personPersonid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','Sc', 'WITH', 'PS.schoolid = Sc.schoolid')
            ->groupBy('PS.schoolid')
            ->orderBy( 'PS.schoolid')
            ->getQuery()
            ->getResult();

        return $this->_schoolId;
    }

}