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

        /*
         * ToDo: Consultar BD para obtener tabla de preguntas y respuestas.
         * */

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:resumen.html.twig', array(
            'title' => $details[0]['title'],
            'date' => $details[0]['date']->format('j/M/Y \@ g:i a'),
        ));

    }

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

}