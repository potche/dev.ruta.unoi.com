<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:18
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListarController extends Controller
{
    public function indexAction(){

        return $this->render('@UNOEvaluaciones/Evaluaciones/listar.html.twig');
    }
}