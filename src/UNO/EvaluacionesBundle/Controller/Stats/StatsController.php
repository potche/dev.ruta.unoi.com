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
    private $_personId;
    /**
     * @Route("/estadisticas")
     *
     * Muestra las estadisticas
     */
    public function indexAction(Request $request){
        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $this->getProfile($session);
            $this->getPersonId($session);
            if( in_array('SuperAdmin', $this->_profile) ){
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas Admin',
                    'dat' => $this->getResults()
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

    private function getProfile($session){
        $profileJson = json_decode($session->get('profileS'));

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }

    }

    private function getPersonId($session){
        $this->_personId = $session->get('personIdS');
    }

    private function getResults() {
        $resultsArray = $this->getSurveyResults();


        if( !empty($resultsArray) ){
            return $this->processResults($resultsArray);
        }
    }

    private function getSurveyResults() {

        $em = $this->getDoctrine()->getManager();
        if( !in_array('SuperAdmin', $this->_profile) ){
            $resultsArray = $this->getSurveyResultsP();
        }else{
            $resultsArray = $this->getSurveyResultsA();
        }
        $query = $em->createQuery('SELECT P.personid, PS.schoolid, S.title, QS.order, Q.question, Sub.subcategory, A.answer, A.comment
                                  FROM UNOEvaluacionesBundle:Answer A
                                  INNER JOIN UNOEvaluacionesBundle:Optionxquestion OQ WITH A.optionxquestion = OQ.optionxquestionId
                                  INNER JOIN UNOEvaluacionesBundle:Questionxsurvey QS WITH OQ.questionxsurvey = QS.questionxsurveyId
                                  INNER JOIN UNOEvaluacionesBundle:Question Q WITH QS.questionQuestionid = Q.questionid
                                  INNER JOIN UNOEvaluacionesBundle:Subcategory Sub WITH Q.subcategorySubcategoryid = Sub.subcategoryid
                                  INNER JOIN UNOEvaluacionesBundle:Survey S WITH QS.surveySurveyid = S.surveyid
                                  INNER JOIN UNOEvaluacionesBundle:Personschool PS WITH A.personPersonid = PS.personid
                                  INNER JOIN UNOEvaluacionesBundle:Person P WITH PS.personid = P.personid
                                  GROUP BY A.personPersonid, PS.schoolid, S.surveyid, QS.order
                                  ORDER BY A.personPersonid, PS.schoolid, S.surveyid, QS.order'
                                );
            //->setParameter('price', '19.99');
        $results = $query->getResult();
        return $results;
    }

    private function getSurveyResultsP() {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $results = $qb->select('P.personid',
            'PS.schoolid',
            'S.title',
            'QS.order',
            'Q.question',
            'Sub.subcategory',
            'A.answer',
            'A.comment'
        )
            ->from('UNOEvaluacionesBundle:Answer','A')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'A.personPersonid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->Where('A.personPersonid = :personId')
            ->setParameters(array('personId' => $this->_personId))
            ->groupBy('A.personPersonid', 'PS.schoolid', 'S.surveyid', 'QS.order')
            ->orderBy( 'A.personPersonid')
            ->addOrderBy('PS.schoolid')
            ->addOrderBy('S.surveyid')
            ->addOrderBy('QS.order')
            ->getQuery()
            ->getResult();

        return $results;
    }

    private function processResults($resultsArray){
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
        $json = "[
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

        return $json;

    }

    private function getPorcentaje($total, $parte, $redondear = 2) {
        return round($parte / $total * 100, $redondear);

    }

}