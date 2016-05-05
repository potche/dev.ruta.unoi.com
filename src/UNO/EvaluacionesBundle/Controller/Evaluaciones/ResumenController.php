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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login',array(
                'redirect' => 'resumen',
                'with' => $surveyId
            ));
        }

        // Autorización de evaluación
        if(!Utils::isSurveyAuthorized($session,$surveyId)) {
            throw new AccessDeniedHttpException('No estás autorizado para visualizar este contenido');
        }

        $personId = $session->get('personIdS');

        $survey = $this->getSurvey($surveyId,$personId);

        if(isset($survey['Error'])){
            throw new NotFoundHttpException('Resumen no encontrado para esta evaluación');
        }

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:resumen.html.twig', array(
            'survey' => $survey['persons'][0]['surveys'][0],
            'date' => $survey['persons'][0]['surveys'][0]['answerDate']['date']
        ));
    }


    private function getSurvey($surveyId, $personId){

        return json_decode(file_get_contents($this->generateUrl('APIResultBySurveyPerson',array(
            'surveyId' => $surveyId,
            'personId' => $personId
        ),true),false),true);
    }


}