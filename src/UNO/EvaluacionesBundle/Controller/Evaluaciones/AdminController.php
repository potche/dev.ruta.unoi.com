<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 13/10/15
 * Time: 11:35 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminController extends Controller {


    /**
     * Controlador principal que maneja la vista del administrador, y muestra la opción para crear una evaluación
     *
     * @param Request $request
     * @return Response
     * @author julio
     * @version 0.2.0
     */
    public function indexAction(Request $request){

        $session = $request->getSession();

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }

        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig', array(
            'surveylist' => $this->getSurveys(),
            'stats' => $this->getStatistics()
        ));
    }

    private function getStatistics(){

        return json_decode(file_get_contents($this->generateUrl('APIStatsProgress',array(),true),false),true);
    }

    private function getSurveys(){

        return json_decode(file_get_contents($this->generateUrl('APISurveys',array(),true),false),true);
    }

    public function setSurveyStatusAction(Request $request) {

        $surveyId = $request->request->get('surveyid');
        $status = $request->request->get('surveyStatus');

        if (!$surveyId || !$status) {

            $response = json_encode(array('message' => 'Petición malformada'));
            return new Response($response, 500, array(
                'Content-Type' => 'application/json'
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $survey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Survey')
            ->findOneBy(array(
                'surveyid' => $surveyId
            ));

        $survey->setActive($status === 'true');
        $em->flush();

        $response = json_encode(array('message' => 'Se ha actualizado con exito'));
        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }
}