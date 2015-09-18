<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:33
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ResponderController extends Controller
{
    public function indexAction(){

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:responder.html.twig');
    }
}