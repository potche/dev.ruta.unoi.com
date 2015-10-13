<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 12/10/15
 * Time: 10:14 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

define('ANSWERED_LOG_CODE', '4');

class ResumenController extends Controller
{
    /**
     *
     * Método principal del controlador responsable de la lógica para mostrar el resumen de una evaluación ya respondida
     *
     * @param Request $request
     * @param $surveyId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request, $surveyId){

        // Validación de sesión activa
        $session = $request->getSession();

        if (!$session->has('logged_in')) {
            return $this->redirectToRoute('login');
        }

        // Autorización de evaluación
        if(!Utils::isSurveyAuthorized($session,$surveyId)) {

            return $this->render('@UNOEvaluaciones/Evaluaciones/responderError.html.twig',array(
                'title'=>'Error',
                'message'=>'Lo sentimos, el contenido que buscas es erróneo',
            ));
        }

        $personId = $session->get('personIdS');
        $details = $this->getSurveyLog($personId,$surveyId,ANSWERED_LOG_CODE);

        if(empty($details)) {

            return $this->render('UNOEvaluacionesBundle:Evaluaciones:responderError.html.twig', array(
               'title' => 'Error',
                'message' => 'Necesitas responder esta evaluación antes de ver su resúmen'
            ));
        }

        $results = $this->getSurveyResults($surveyId,$personId);

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:resumen.html.twig', array(
            'title' => $details[0]['title'],
            'date' => $details[0]['date']->format('j/M/Y \@ g:i a'),
            'results' => $results
        ));
    }

    /**
     * Método que obtiene y devuelve título de evaluación, fecha y hora en que respondió el usuario
     *
     *
     * @param $personId
     * @param $surveyId
     * @param $action
     * @return mixed
     */

    private function getSurveyLog($personId, $surveyId, $action){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $eval = $qb->select('su.title', 'log.date')
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->leftJoin('UNOEvaluacionesBundle:Log','log', 'WITH', 'su.surveyid = log.surveySurveyid')
            ->where('log.surveySurveyid = :surveyId')
            ->andWhere('log.personPersonid = :personId')
            ->andWhere('log.actionaction = :logcode')
            ->orderBy('log.date')
            ->setMaxResults(1)
            ->setParameters(array(
                'personId' => $personId,
                'surveyId' => $surveyId,
                'logcode' => $action,
            ))
            ->getQuery()
            ->getResult();

        return $eval;
    }

    /**
     *
     * Método que obtiene y devuelve:
     * orden, pregunta, categoría, respuesta, comentario de los reactivos de una evaluación
     *
     * @param $surveyId
     * @param $personId
     * @return mixed
     */


    private function getSurveyResults($surveyId, $personId) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $results = $qb->select('qxs.order', 'qu.question', 'sub.subcategory','ans.answer','ans.comment')
            ->from('UNOEvaluacionesBundle:Answer','ans')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq', 'WITH', 'ans.optionxquestion = oxq.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs', 'WITH', 'oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','qu', 'WITH', 'qxs.questionQuestionid = qu.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sub', 'WITH', 'qu.subcategorySubcategoryid = sub.subcategoryid')
            ->where('qxs.surveySurveyid = :surveyId')
            ->andWhere('ans.personPersonid = :personId')
            ->orderBy('qxs.order')
            ->setParameters(array(
                'personId' => $personId,
                'surveyId' => $surveyId,
            ))
            ->getQuery()
            ->getResult();

        return $results;

    }
}