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

    /**
     * @Route("/estadisticas")
     *
     * Muestra las estadisticas
     */
    public function indexAction(Request $request){
        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {

            return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                'title' => 'Estadisticas',
                'dat' => $this->getResults()
            ));
        }else{
            return $this->redirect("/");
        }
    }

    private function getSurveyResultsGrl() {
        $personId = 3066990;
        $surveyId = 1;
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
            ->where('QS.surveySurveyid = :surveyId')
            ->andWhere('A.personPersonid = :personId')
            ->groupBy('PS.schoolid' , 'S.surveyid' , 'QS.order')
            ->orderBy( 'PS.schoolid')
            ->addOrderBy('S.surveyid')
            ->addOrderBy('QS.order')

            ->setParameters(array(
                'personId' => $personId,
                'surveyId' => $surveyId,
            ))

            ->getQuery()
            ->getResult();

        print_r( $results );
    }

    private function getResults() {
        $personId = 3066990;
        $surveyId = 1;
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $results = $qb->select('A.answer as name')
            ->addSelect('count(A.answer) as y')
            ->addSelect("case when(A.answer='Sí') then 'true' else 'false' end as sliced")
            ->addSelect("case when(A.answer='Sí') then 'true' else 'false' end as selected")
            ->from('UNOEvaluacionesBundle:Answer','A')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->where('QS.surveySurveyid = :surveyId')
            ->andWhere('A.personPersonid = :personId')
            ->groupBy('A.answer')
            ->orderBy( 'A.answer')
            ->setParameters(array(
                'personId' => $personId,
                'surveyId' => $surveyId,
            ))
            ->getQuery()
            ->getResult();
        print_r($results);
        return json_encode($results);
    }

}