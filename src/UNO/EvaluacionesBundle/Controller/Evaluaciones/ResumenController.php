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

class ResumenController extends Controller
{

    public function indexAction(Request $request, $surveyId){

        $session = $request->getSession();

        if (!$session->has('logged_in')) {
            return $this->redirectToRoute('login');
        }

        if(!Utils::isSurveyAuthorized($session,$surveyId)) {

            return $this->render('@UNOEvaluaciones/Evaluaciones/responderError.html.twig',array(
                'title'=>'Error',
                'message'=>'Lo sentimos, el contenido que buscas es erróneo',
            ));
        }

        $personId = $session->get('personIdS');

        return new Response("calling view");

    }

}