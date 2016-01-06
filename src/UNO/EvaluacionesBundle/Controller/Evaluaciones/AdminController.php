<?php

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminController extends Controller {

    /**
     * Controlador principal que maneja la vista del administrador, y muestra la opción para crear una evaluación
     *
     * @param Request $request
     * @return Response
     * @author julio
     * @version 0.3.0
     */
    public function indexAction(Request $request){

        $session = $request->getSession();

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login',array(
                'redirect' => 'evaluaciones',
                'with' => 'none'
            ));
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }

        $surveylist = $this->getSurveys();

        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig', array(
            'surveylist' => $surveylist,
            'surveyids' =>array_keys($surveylist)
        ));
    }

    private function getSurveys(){

        return json_decode(file_get_contents($this->generateUrl('APISurveys',array(),true),false),true);
    }

    public function setSurveyStatusAction(Request $request) {

        $surveyId = $request->request->get('surveyid');
        $status = $request->request->get('surveyStatus');
        $response = new JsonResponse();


        if (!$surveyId || !$status) {

            $response->setData(array(
                'status' => '400',
                'message' => 'Petición malformada'
            ));

        }else{

            $em = $this->getDoctrine()->getManager();
            $survey = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Survey')
                ->findOneBy(array(
                    'surveyid' => $surveyId
                ));

            $survey->setActive($status === 'true');
            $em->flush();

            $response->setData(array(
                'status' => '200',
                'message' => 'Se ha modificado el estatus de la evaluación'
            ));
        }
        return $response;
    }
}