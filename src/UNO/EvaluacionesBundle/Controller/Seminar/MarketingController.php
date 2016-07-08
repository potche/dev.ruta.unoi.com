<?php

namespace UNO\EvaluacionesBundle\Controller\Seminar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Class MarketingController
 * @package UNO\EvaluacionesBundle\Controller\Seminar
 */
class MarketingController extends Controller {

    /**
     * @Route("/seminarMarketing")
     *
     */
    public function indexAction(Request $request){

        if(!Utils::isUserLoggedIn($request->getSession())){

            return $this->redirectToRoute('login',array(
                'redirect' => 'seminarMarketing',
                'with' => 'none'
            ));
        }

        return $this->render('UNOEvaluacionesBundle:Seminar:marketing.html.twig');
    }

}