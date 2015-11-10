<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 30/10/15
 * Time: 10:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

class AjaxStatsController extends Controller{

    /**
     * @Route("/ajax/stats")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function indexAction(Request $request){

        $session = $request->getSession();
        $session->start();
        if ($request->getMethod() == 'POST') {
            $personId = $request->get('personId');
            $title = $request->get('title');
            $survey = $this->getSurveyResponse($personId, $title);
            $tableSurvey = $this->creaTable($survey, $title);
            return new response($tableSurvey);
        }else{
            return new response($request->getMethod());
        }
    }

    /**
     * @param $personId
     * @param $title
     * @return mixed
     *
     * obtiene la evaluacion selecciona
     */
    private function getSurveyResponse($personId, $title) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyResponse = $qb->select("QS.order, Q.question, A.answer, A.comment")
            ->from('UNOEvaluacionesBundle:Answer','A')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->where('A.personPersonid = :personid')
            ->andWhere('S.title = :title')
            ->setParameters(array('personid' => $personId, 'title' => $title))
            ->getQuery()
            ->getResult();
        return ($_surveyResponse);
    }

    /**
     * @param $survey
     * @param $title
     * @return string
     *
     * crea una tabla con la informacion obtenida en el metodo getSurveyResponse()
     */
    private function creaTable($survey,$title){
        $table ='
        <div class="row">
            <div class="block-content">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="block">
                        <div class="block-title">
                            <h2>Evaluación <strong>'.$title.'</strong></h2>
                        </div>
                        <div class="table-responsive">
                            <p><em>Detalle de la Evaluación.</em></p>
                            <table id="example-datatable" class="table table-vcenter table-condensed table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Orden</th>
                                        <th class="text-center">Pregunta</th>
                                        <th class="text-center">Respuesta</th>
                                        <th class="text-center">Comentario</th>
                                    </tr>
                                </thead>
                                <tbody>';
        foreach($survey as $value){
            $table .= '
                                    <tr>
                                        <td>'.$value['order'].'</td>
                                        <td>'.$value['question'].'</td>
                                        <td>'.$value['answer'].'</td>
                                        <td>'.$value['comment'].'</td>
                                    </tr>';
        }

        $table .='
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $table;
    }

}
