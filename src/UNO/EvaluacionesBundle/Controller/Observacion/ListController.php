<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 30/10/15
 * Time: 10:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Observacion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ListController
 * @package UNO\EvaluacionesBundle\Controller\Observacion
 */
class ListController extends Controller{

    /**
     * @Route("/observacion")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function indexAction(Request $request){
        $baseUrl = "http://dev.ruta.unoi.com".$this->container->get('router')->getContext()->getBaseUrl();
        $schoolListAPI = file_get_contents("$baseUrl/api/v0/catalog/schools", false);
        return $this->render('UNOEvaluacionesBundle:Observacion:index.html.twig', array(
                'schoolList' => $schoolListAPI
            ));

    }

    private function createSchoolList($schoolList){
        $arraySchool = array();
        foreach(json_decode($schoolList) as $value){
            array_push( $arraySchool, $value->schoolid .'-'. $value->school );
        }

        return json_encode($arraySchool);
    }

}
