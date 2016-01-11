<?php

namespace UNO\EvaluacionesBundle\Controller\Tutorial;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

}