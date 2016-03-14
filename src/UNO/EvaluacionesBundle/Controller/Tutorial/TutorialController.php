<?php

namespace UNO\EvaluacionesBundle\Controller\Tutorial;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

class TutorialController extends Controller {


    public function indexAction(Request $request){

        if(!Utils::isUserLoggedIn($request->getSession())){

            return $this->redirectToRoute('login',array(
                'redirect' => 'tutorials',
                'with' => 'none'
            ));
        }

        return $this->render('@UNOEvaluaciones/Tutorial/tutorial.html.twig');
    }

    public function ajaxtourAction(Request $request){

        $session =  $request->getSession();

        $personId = $request->request->get('personid');
        $status = $request->request->get('status');
        $response = new JsonResponse();

        if ($personId == null || $status == null) {

            $response->setData(array(
                'status' => '400',
                'message' => 'Petición malformada'
            ));

        }else{

            $em = $this->getDoctrine()->getManager();
            $person = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Person')
                ->findOneBy(array(
                    'personid' => $personId
                ));

            $person->setTourenabled($status === '1' ? 1 : 0);
            $em->flush();
            $session->set('tourEnabled',$status === '1' ? 1 : 0);

            $response->setData(array(
                'status' => '200',
                'message' => 'Cambio con éxito'
            ));
        }
        return $response;
    }
}