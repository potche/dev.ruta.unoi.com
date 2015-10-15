<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 13/10/15
 * Time: 12:18
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InicioController extends Controller{

    public function indexAction(Request $request) {
        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            return $this->render('UNOEvaluacionesBundle:Evaluaciones:inicio.html.twig');
        }else{
            return $this->redirect("/");
        }
    }


}