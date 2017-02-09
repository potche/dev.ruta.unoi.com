<?php

namespace UNO\EvaluacionesBundle\Controller\API_SimuladorCostos;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\API_SimuladorCostos\APIUtils;
use UNO\EvaluacionesBundle\Entity\VPuntos;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 9/12/16
 * Time: 2:40 PM
 */

ini_set('memory_limit', '-1');

/**
 * @Route("/api/v0/simCosts")
 *
 */
class PuntosController extends Controller{

    private $_results = array();
    private $_status;
    private $_message;

    /**
     * @Route("/colegioPuntos")
     *
     */
    public function indexAction(){

        $APIUtils = new APIUtils();

        $this->getQuery();

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);

    }

    private function getQuery(){

        $colegio = array();

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $VPuntos = $qb->select('VP.colegioId, VP.colegio, VP.matricula, VP.renovacionK, VP.renovacionP, 
                    VP.renovacionS, VP.totalAlumnos, VP.totalAluas, VP.aulasDevengadas, VP.alumnosPorAula, 
                    VP.puntosPorAulaDevengada, VP.puntosPorAlumno, VP.ciclos, VP.puntosOfrecer')
            ->from('UNOEvaluacionesBundle:VPuntos', 'VP')
            ->getQuery()
            ->getResult();

        if($VPuntos){

            foreach ($VPuntos as $row){
                array_push($colegio,
                    array(
                        "colegioId" => $row["colegioId"],
                        "colegio" => $row["colegio"],
                        "data" => array(
                            "matricula" => $row["matricula"],
                            "renovacionK" => $row["renovacionK"],
                            "renovacionP" => $row["renovacionP"],
                            "renovacionS" => $row["renovacionS"],
                            "totalAlumnos" => $row["totalAlumnos"],
                            "totalAluas" => $row["totalAluas"],
                            "aulasDevengadas" => $row["aulasDevengadas"],
                            "alumnosPorAula" => $row["alumnosPorAula"],
                            "puntosPorAulaDevengada" => $row["puntosPorAulaDevengada"],
                            "puntosPorAlumno" => $row["puntosPorAlumno"],
                            "ciclos" => $row["ciclos"],
                            "puntosOfrecer" => $row["puntosOfrecer"]
                        )
                    ));
            }

            $this->_results = $colegio;
            $this->_status = 200;

        }else {
            $this->_status = 400;
        }


    }

}