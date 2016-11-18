<?php

namespace UNO\EvaluacionesBundle\Controller\API_SimuladorCostos;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\API_SimuladorCostos\APIUtils;
use UNO\EvaluacionesBundle\Entity\CodigoPostal;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 11/11/16
 * Time: 2:40 PM
 */

ini_set('memory_limit', '-1');

/**
 * @Route("/api/v0/simCosts")
 *
 */
class CPController extends Controller{

    private $_results = array();
    private $_status;
    private $_message;

    /**
     * @Route("/cp")
     *
     */
    public function indexAction(){

        $APIUtils = new APIUtils();

        $this->getQueryCP('CP.d_codigo != :cp', array('cp' => 'null'), 'edo');

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);

    }

    /**
     * @Route("/cp/catalogue")
     *
     */
    public function cpCatalogueAction(){

        $APIUtils = new APIUtils();

        $this->getQueryCP('CP.d_codigo != :cp', array('cp' => 'null'));

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);

    }

    /**
     * @Route("/cp/{cp}")
     *
     */
    public function cpAction($cp){

        $APIUtils = new APIUtils();

        if(strlen($cp) == 5){

            $this->getQueryCP('CP.d_codigo = :cp', array('cp' => $cp), 'cp');

        }else{

            $this->_status = 400;

        }

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);
    }

    /**
     * @Route("/cp/edo/{edo}")
     *
     */
    public function edoAction($edo){

        $APIUtils = new APIUtils();

        if(strlen($edo) == 2) {

            $this->getQueryCP('CP.c_estado = :edo', array('edo' => $edo), 'edo');

        }else{

            $this->_status = 400;
            $this->_message = 'la longitud del parametro es invalida';
        }

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);

    }

    private function getQueryCP($where, array $parameters = null, $type = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $CodigoPostal = $qb->select('CP.d_codigo, CP.d_asenta, CP.D_mnpio, CP.d_estado')
            ->from('UNOEvaluacionesBundle:CodigoPostal', 'CP')
            ->where($where)
            ->setParameters($parameters)
            //->setMaxResults(2000)
            ->getQuery()
            ->getResult();

        if($CodigoPostal) {

            switch($type) {
                case 'cp':
                    $this->prepareJsonCP($CodigoPostal);
                    break;
                case 'edo':
                    $edos = array_unique(array_column($CodigoPostal, 'd_estado'));
                    $this->prepareJsonEdo($CodigoPostal, $edos);
                    break;
                default:
                    //print_r($CodigoPostal);
                    $this->prepareJsonCatalogue($CodigoPostal);
                    break;
            }

        }else{

            $this->_status = 200;
            $this->_message = 'La petición se ha completado con éxito pero su respuesta no tiene ningún contenido';

        }

    }

    private function prepareJsonCP($CodigoPostal){

        $responseCP = array();
        $colonias = array();

        foreach ($CodigoPostal as $rowCP) {

            array_push($colonias, $rowCP['d_asenta']);
            $responseCP['cp'] = $rowCP['d_codigo'];
            $responseCP['municipio'] = $rowCP['D_mnpio'];
            $responseCP['estado'] = $rowCP['d_estado'];

        }

        $responseCP['colonias'] = $colonias;

        $this->_status = 200;
        $this->_results = $responseCP;
    }

    private function prepareJsonEdo($CodigoPostal, $edos){

        $responseCP = array();
        $estadosArr = array();

        foreach ($edos as $edo) {

            $edoGroup = array_filter($CodigoPostal, function ($var) use ($edo) {
                return ($var['d_estado'] == $edo);
            });

            $mnpios = array_unique(array_column($edoGroup, 'D_mnpio'));
            $municipiosArr = array();

            foreach ($mnpios as $mnpio) {

                $mnpioGroup = array_filter($CodigoPostal, function ($var) use ($mnpio) {
                    return ($var['D_mnpio'] == $mnpio);
                });

                $cps = array_unique(array_column($mnpioGroup, 'd_codigo'));
                $cpsArr = array();

                foreach ($cps as $cp) {

                    $cpGroup = array_filter($mnpioGroup, function ($var) use ($cp) {
                        return ($var['d_codigo'] == $cp);
                    });

                    $coloniasArr = array();

                    foreach ($cpGroup as $row) {

                        array_push($coloniasArr, $row['d_asenta']);
                        $cpArray['cp'] = $row['d_codigo'];

                    }

                    $cpArray['colonias'] = $coloniasArr;
                    array_push($cpsArr, $cpArray);
                }

                $municipioArr['municipio'] = $mnpio;
                $municipioArr['cps'] = $cpsArr;
                array_push($municipiosArr, $municipioArr);
            }

            $estadosArr['estado'] = $edo;
            $estadosArr['municipios'] = $municipiosArr;

            array_push($responseCP, $estadosArr);
        }

        $this->_status = 200;
        $this->_results = $responseCP;
    }

    private function prepareJsonCatalogue($CodigoPostal){

        $responseCP = array();
/*
        foreach ($CodigoPostal as $rowCP) {
            array_push($responseCP, array(
                'cp' => $rowCP['d_codigo'],
                'colonia' => $rowCP['d_asenta'],
                'municipio' => $rowCP['D_mnpio'],
                'estado' => $rowCP['d_estado']
            ));
        }
*/

        $this->_status = 200;
        $this->_results = $CodigoPostal;
    }

}