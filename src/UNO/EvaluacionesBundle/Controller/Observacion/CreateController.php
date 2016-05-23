<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 23/05/16
 * Time: 12:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Observacion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CreateController
 * @package UNO\EvaluacionesBundle\Controller\Observacion
 */
class CreateController extends Controller{

    /**
     * @Route("/observacion/crear/{schoolId}/{personId}/{assignedId}")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function indexAction(Request $request, $schoolId, $personId, $assignedId){
        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseURL = $this->container->get('router')->getContext()->getBaseUrl();
        
        $baseUrl = "http://dev.ruta.unoi.com".$this->container->get('router')->getContext()->getBaseUrl();

        //$schoolListAPI = json_decode(file_get_contents("$baseUrl/api/v0/catalog/schools", false), true);
        
        return $this->render('UNOEvaluacionesBundle:Observacion:create.html.twig', array(
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
