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
        

        return $this->render('@UNOEvaluaciones/Evaluaciones/resumen.html.twig', array());

    }

    private function getSurvey($personId, $surveyId, $action){

        /*
         * ToDo: query para traer título, fecha y hora de respuesta.
         */
    }

}