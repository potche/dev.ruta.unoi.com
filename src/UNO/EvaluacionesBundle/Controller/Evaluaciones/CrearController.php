<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 22/10/15
 * Time: 11:27 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;

class CrearController extends Controller {

    public function indexAction(Request $request){

        $session = $request->getSession();

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }

        return $this->render('UNOEvaluacionesBundle:Crear:crear.html.twig');

    }

    public function saveAction(Request $request){

        if(!isset($_POST['eval'])) {

            throw new InternalErrorException('Ha ocurrido un error al procesar esta petición');
        }

        /**
         * ToDo: implementar lógica para almacenar evaluación a partir de la petición
         */

        return $this->redirectToRoute('evaluaciones');
    }

}