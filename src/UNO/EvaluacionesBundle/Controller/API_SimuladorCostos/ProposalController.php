<?php

namespace UNO\EvaluacionesBundle\Controller\API_SimuladorCostos;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\API_SimuladorCostos\APIUtils;
use UNO\EvaluacionesBundle\Entity\Proposal;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 17/11/16
 * Time: 11:07 AM
 */


/**
 * @Route("/api/v0/simCosts")
 *
 */
class ProposalController extends Controller{

    private $_results = array();
    private $_status = 200;
    private $_message;

    private $_invalid = array();
    private $_idEscuela;
    private $_nombreEscuela;


    /**
     * @Route("/proposal")
     *
     */
    public function indexAction(Request $request){

        $APIUtils = new APIUtils();

        if ( $request->isMethod('POST') ){

            $content = $request->getContent();

            $this->isValidJsonString($content);

        }else{

            $this->_status = 405;

        }

        return new JsonResponse($APIUtils->jsonResponse( $this->_results, $this->_status, $this->_message ), $this->_status);
    }

    public function isValidJsonString($content) {

        //valida que el el RAW no venga vacío
        if (!empty($content)){

            $params = json_decode($content);
            //valida que el json
            if($params) {
                $dir = array(
                    "cp" => $params->direccion->cp,
                    "colonia" => $params->direccion->colonia,
                    "calle" => $params->direccion->calle,
                    "numero" => $params->direccion->numero
                );



                if ( is_null($params->escuela->idEscuela) && !$this->dir($dir) ) {

                    $this->_status = 400;
                    $this->_message = '¿Escuela nueva?, requiere dirección';

                }elseif ( !is_null($params->escuela->idEscuela) && $this->dir($dir) ) {

                    $this->_status = 400;
                    $this->_message = 'Escuela existente, no requiere dirección';

                }else{

                    $this->addProposal($params);
                    $this->_status = 200;

                }

            }else{
                $this->_status = 400;
                $this->_message = 'json invalido';
            }

        }else{
            $this->_status = 400;
            $this->_message = 'json invalido';
        }
    }

    /**
     * @return bool
     * inserta nueva proposal
     */
    private function addProposal($params){

        $em = $this->getDoctrine()->getManager();

        try{
            $Proposal = new Proposal();
            $Proposal->setSchoolId($params->escuela->idEscuela);
            $Proposal->setSchool($params->escuela->nombreEscuela);

            $Proposal->setCp( $this->stringEmpty($params->direccion->cp) );
            $Proposal->setColonia( $this->stringEmpty($params->direccion->colonia) );
            $Proposal->setCalle( $this->stringEmpty($params->direccion->calle) );
            $Proposal->setNumero( $this->stringEmpty($params->direccion->numero) );

            $Proposal->setAulaDigital( $this->intEmpty($params->items->aulaDigital) );
            $Proposal->setMakerCart( $this->intEmpty($params->items->makerCart) );
            $Proposal->setAulaMaker( $this->intEmpty($params->items->aulaMaker) );
            $Proposal->setProyector( $this->intEmpty($params->items->proyector) );
            $Proposal->setTelepresencia( $this->intEmpty($params->items->telepresencia) );
            $Proposal->setAceleracon( $this->intEmpty($params->items->aceleracon) );
            $Proposal->setCertificacion( $this->intEmpty($params->items->certificacion) );
            $Proposal->setDesarrollo( $this->intEmpty($params->items->desarrollo) );

            $Proposal->setPuntosTotales($params->puntosTotales);
            $Proposal->setPuntosUsados($params->puntosUsados);
            $Proposal->setPuntosSaldo($params->puntosSaldo);
            $Proposal->setTotalPesos($params->totalPesos);
            $Proposal->setTotalAños($params->totalAños);
            $Proposal->setTotalAportacion($params->totalAportacion);
            $Proposal->setTotalAlumnos($params->totalAlumnos);
            $Proposal->setPorcentajeParticipacion($params->porcentajeParticipacion);
            $Proposal->setPrecioVenta($params->precioVenta);

            $Proposal->setDateregister(new \DateTime());

            $em->persist($Proposal);
            $em->flush();
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    /**
     * @param mixed $idEscuela
     */
    private function setIdEscuela($idEscuela){
        if(!is_null($idEscuela)){
            if(is_int($idEscuela)){
                $this->_idEscuela = $idEscuela;
            }else{
                array_push($this->_invalid,'idEscuela');
            }
        }
    }

    /**
     * @return mixed
     */
    private function getIdEscuela(){
        return $this->_idEscuela;
    }

    /**
     * @param mixed $nombreEscuela
     */
    public function setNombreEscuela($nombreEscuela){
        if(!is_null($nombreEscuela)){
            if(is_string($nombreEscuela)){
                $this->_nombreEscuela = $nombreEscuela;
            }else{
                array_push($this->_invalid,'nombreEscuela');
            }
        }

    }

    /**
     * @return mixed
     */
    public function getNombreEscuela()
    {
        return $this->_nombreEscuela;
    }

    private function dir($params){
        if(
            $this->isStringAndNotNull($params['cp']) ||
            $this->isStringAndNotNull($params['colonia']) ||
            $this->isStringAndNotNull($params['calle']) ||
            $this->isStringAndNotNull($params['numero'])
        ){
            return true;
        }else{
            return false;
        }
    }

    private function isStringAndNotNull($var){

        if(!is_null($var) && $var != ''){
            print_r($var);
            return true;
        }else{
            return false;
        }
    }

    private function stringEmpty($var){
        if( is_null($var) || (is_string($var) && $var != '') ){
            return $var;
        }else{
            return null;
        }
    }

    private function intEmpty($var){
        if( is_null($var) || is_int($var) ){
            return $var;
        }else{
            return 0;
        }
    }

    private function floatEmpty($var){
        if( is_null($var) || is_float($var) ){
            return $var;
        }else{
            return 0.00;
        }
    }
}