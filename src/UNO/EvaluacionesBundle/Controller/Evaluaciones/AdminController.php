<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 13/10/15
 * Time: 11:35 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    public function indexAction(Request $request){



        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig');

    }

}