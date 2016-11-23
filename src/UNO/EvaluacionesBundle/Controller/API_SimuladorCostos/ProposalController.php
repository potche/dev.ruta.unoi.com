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
use UNO\EvaluacionesBundle\Controller\API_SimuladorCostos\BodyMail;

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

    private $_schoolContact;
    private $_datSchoolContact;
    private $_datVendedor;

    private $_name;
    private $_to;

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
                }elseif (!filter_var($params->contacto->email, FILTER_VALIDATE_EMAIL)) {
                    $this->_status = 400;
                    $this->_message = 'El correo de Contacto no es valido';
                }elseif (!$this->validateVendedorDB($params->vendedorId)) {
                    $this->_status = 400;
                    $this->_message = 'El campo vendedorId no es valido';
                }elseif (!is_null($params->escuela->idEscuela) && !$this->validateSchoolDB($params->escuela->idEscuela)) {
                    $this->_status = 400;
                    $this->_message = 'El campo idEscuela no es valido';
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

            $Proposal->setNombreContacto( $this->stringEmpty($params->contacto->nombre) );
            $Proposal->setEmailContacto( $this->stringEmpty($params->contacto->email) );

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

            $Proposal->setVendedorId($params->vendedorId);

            $Proposal->setDateregister(new \DateTime());

            $em->persist($Proposal);
            $em->flush();

            $this->sendMail($params);

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

    /**
     * obtiene los nombres y correo de los directores del colegio
     */
    private function getSchoolContact($schoolId) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $this->_schoolContact = $qb->select("concat(P.name, ' ', P.surname) as nombre, P.email")
            ->from('UNOEvaluacionesBundle:Person','P')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'P.personid = PS.personid')
            ->where('PS.schoolid = :schoolId')
            ->andWhere('PS.profileid = 19')
            ->setParameter('PS.schoolid', $schoolId)
            ->getQuery()
            ->getResult();

    }

    /**
     * @return bool
     * revisa que se encuentre el colegio
     */
    private function validateSchoolDB($schoolId){

        $ok = true;
        $em = $this->getDoctrine()->getManager();
        $this->_datSchoolContact = $em->getRepository('UNOEvaluacionesBundle:Person')->findBy(array('schoolid' => $schoolId));

        if(!$this->_datSchoolContact) {
            //el usuario y password no se encuentran en la BD
            $ok = false;
        }

        return $ok;
    }

    /**
     * @return bool
     * revisa que se encuentre el vendedor
     */
    private function validateVendedorDB($personId){

        $ok = true;
        $em = $this->getDoctrine()->getManager();
        $this->_datVendedor = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid' => $personId));

        if(!$this->_datVendedor) {
            //el usuario y password no se encuentran en la BD
            $ok = false;
        }

        return $ok;
    }

    private function parseContact(){
        /*
          $to  = 'aidan@example.com' . ', '; // note the comma
          $to .= 'wez@example.com';
        */
        if($this->_datSchoolContact){
            print_r($this->_datSchoolContact);
        }else {
            $this->_name = strtoupper($params->contacto->nombre);
            $this->_to = $params->contacto->email;
        }

    }

    /**
     * envia el correo de la propuesta
     */
    private function sendMail($params) {
        $BodyMail = new BodyMail();

        $name = strtoupper($params->contacto->nombre);
        $to = $params->contacto->email;

        $parameters = array(
            'aulaDigital' => $this->intEmpty($params->items->aulaDigital),
            'makerCart' => $this->intEmpty($params->items->makerCart),
            'aulaMaker' => $this->intEmpty($params->items->aulaMaker),
            'proyector' => $this->intEmpty($params->items->proyector),
            'telepresencia' => $this->intEmpty($params->items->telepresencia),
            'aceleracon' => $this->intEmpty($params->items->aceleracon),
            'certificacion' => $this->intEmpty($params->items->certificacion),
            'desarrollo' => $this->intEmpty($params->items->desarrollo),
            'puntosTotales' => $params->puntosTotales,
            'puntosUsados' => $params->puntosUsados,
            'puntosSaldo' => $params->puntosSaldo,
            'totalPesos' => '$'.number_format($params->totalPesos, 2, '.', ','),
            'totalAños' => $params->totalAños,
            'totalAportacion' => $params->totalAportacion,
            'porcentajeParticipacion' => $params->porcentajeParticipacion
        );

        $subject = "Propuesta UNOi";
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: Simulador Costos UNOi <noreplymx@unoi.com>'."\r\n";
        $headers .= 'Reply-To: NoReply <noreplymx@unoi.com>' . "\r\n";
        $headers .= 'Cc: '. $this->_datVendedor->getEmail() . "\r\n";
        $headers .= 'Bcc: potcheunam@gmail.com' . "\r\n";
        $message = $BodyMail->run($name, $parameters);
        mail($to, $subject, $message, $headers, '-f noreplymx@unoi.com');
    }

}
