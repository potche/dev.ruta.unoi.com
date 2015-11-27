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
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

class AjaxStatsController extends Controller{

    /**
     * @Route("/ajax/detalleStats")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function detalleUserAction(Request $request){

        $session = $request->getSession();
        $session->start();
        if ($request->getMethod() == 'POST') {
            $personId = $request->get('personId');
            $survey = $this->getSurveyResultsGral($personId);
            return new response(json_encode($this->evalUser($survey)));
        }else{
            return new response($request->getMethod());
        }
    }

    /**
     * obtiene toda la informacion de las evaluaciones realizadas por colegio o general
     */
    private function getSurveyResultsGral($personId) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyResultsGral = $qb->select("PS.personid, TRIM(S.title) AS title, O.option as answer, COUNT(DISTINCT (Ans.answerid)) as countAnswer")
            ->from('UNOEvaluacionesBundle:Surveyxprofile ','SP')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', "PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid AND PS.personid = $personId")
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'S.surveyid = SP.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','L', 'WITH', "L.surveySurveyid = S.surveyid AND L.personPersonid = $personId")
            ->leftJoin('UNOEvaluacionesBundle:Action','A', 'WITH', 'A.idaction = L.actionaction')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'O.optionid = OQ.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','Ans', 'WITH', "Ans.optionxquestion = OQ.optionxquestionId AND Ans.personPersonid = $personId")
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere('A.actioncode IS NOT NULL')
            ->groupBy('PS.personid, S.surveyid, S.title , OQ.optionOptionid, O.option')
            ->orderBy( 'PS.schoolid')
            ->getQuery()
            ->getResult();

        return $_surveyResultsGral;
    }

    /**
     * obtiene las evaluaciones que realizo el usuario
     *
     * @param $survey
     * @return array
     */
    private function evalUser($survey){
        $evalUser = array_unique(array_column($survey, 'title'));

        $evalUserArray = array();

        foreach($evalUser as $valTitle){
            $evalTitleArray = array(
                'title' => '',
                'si' => 0,
                'no' => 0,
                'nose' => 0
            );
            foreach($survey as $value){
                $evalTitleArray['title'] = $valTitle;
                if($valTitle == $value['title']){
                    switch ($value['answer']):
                        default:
                            $evalTitleArray['nose'] = $value['countAnswer'];
                            break;
                        case 'Sí':
                            $evalTitleArray['si'] = $value['countAnswer'];
                            break;
                        case 'No':
                            $evalTitleArray['no'] = $value['countAnswer'];
                            break;
                    endswitch;
                }
            }
            array_push($evalUserArray, $evalTitleArray);
        }
        return($evalUserArray);
    }

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
                                        <th class="hidden-sm hidden-xs text-center">#</th>
                                        <th class="text-center">Pregunta</th>
                                        <th class="text-center">
                                            <span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Respuesta</b></span>
                                            <span class="visible-xs-inline"><b><i class="fa fa-pencil-square-o"></i></b></span>
                                        </th>
                                        <th class="text-center">
                                            <span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Comentario</b></span>
                                            <span class="visible-xs-inline"><b><i class="fa fa-commenting-o"></i></b></span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>';
        foreach($survey as $value){
            $table .= '
                                    <tr>
                                        <td class="hidden-sm hidden-xs text-center">'.$value['order'].'</td>
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

    /**
     * @Route("/ajax/searchEval")
     *
     * obtiene las evaluaciones correspondientes a la escuela Filtada
     */
    public function searchEvalAction(Request $request){

        $session = $request->getSession();
        $session->start();
        if ($request->getMethod() == 'POST') {
            if( $request->get('schoolId') == 'all' ){
                $and = "PS.schoolid != ''";
            }else{
                $schoolId = explode('-', $request->get('schoolId'));
                $and = 'PS.schoolid = '.$schoolId[0];
            }
            $surveyList = $this->getSurvey($and);
            return new response(  json_encode($surveyList), 200, array('Content-Type'=>'application/json'));
        }else{
            return new response($request->getMethod());
        }
    }

    /**
     * obtiene toda la informacion de las evaluaciones realizadas por colegio o general
     */
    private function getSurvey($and) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyList = $qb->select("concat(S.surveyid,'-',S.title) as surveyTitle")
            ->from('UNOEvaluacionesBundle:Person','P')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'P.personid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile ','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'S.surveyid = SP.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','L', 'WITH', 'S.surveyid = L.surveySurveyid AND PS.personid = L.personPersonid')
            ->innerJoin('UNOEvaluacionesBundle:Action','A', 'WITH', 'L.actionaction = A.idaction')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Answer','Ans', 'WITH', 'Ans.optionxquestion = OQ.optionxquestionId AND Ans.personPersonid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere('A.actioncode = 004')
            ->andWhere($and)
            ->groupBy('S.surveyid')
            ->orderBy( 'S.surveyid')
            ->getQuery()
            ->getResult();

        $arraySurvey = array();
        foreach($_surveyList as $value){
            array_push($arraySurvey, $value['surveyTitle']);
        }

        return $arraySurvey;
    }

}
