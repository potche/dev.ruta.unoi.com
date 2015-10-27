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

    /**
     *
     * Método principal del controlador para crear evaluaciones
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedHttpException
     * @author julio
     * @version 0.2.0
     */

    public function indexAction(Request $request){

        /**
         * Manejo de sesión
         */

        $session = $request->getSession();

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }

        return $this->render('UNOEvaluacionesBundle:Crear:crear.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws InternalErrorException
     *
     * Método invocado para almacenar una nueva evaluación en la bd
     *
     */

    public function saveAction(Request $request){

        if(!isset($_POST['eval'])) {

            throw new InternalErrorException('Ha ocurrido un error al procesar esta petición');
        }

        var_dump($_POST['eval']);
        exit();

        /**
         * ToDo: implementar lógica para almacenar evaluación a partir de la petición
         */

        return $this->redirectToRoute('evaluaciones');
    }

    private function getCategories(){

    }

    private function getQuestions(){


    }

    private function getLevels(){

    }

    private function getProfiles(){


    }

}