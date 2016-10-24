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
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Class ListController
 * @package UNO\EvaluacionesBundle\Controller\Observacion
 */
class ListController extends Controller{

    private $_profile = array();
    private $_personId;

    /**
     * @Route("/observacion")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function indexAction(Request $request){
        $session = $request->getSession();
        $session->start();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'inicio',
                'with' => 'none'
            ));
        }

        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseURL = $this->container->get('router')->getContext()->getBaseUrl();
        $baseUrl = $scheme.$host.$baseURL;

        $this->setProfile($session->get('profileS'));
        $this->_personId = $session->get('personIdS');

        if(in_array('COACH',$this->_profile)) {
            return $this->render('UNOEvaluacionesBundle:Observacion:index.html.twig', array(
                'schoolList' => json_decode(file_get_contents("$baseUrl/api/v0/catalog/schools", false), true),
                'observationsByCoach' => $this->observationsBy("O.coachId = :coachId", array('coachId' => $session->get('personIdS')) )
            ));
        }else{
            return $this->render('UNOEvaluacionesBundle:Observacion:index.html.twig', array(
                'schoolList' => json_decode(file_get_contents("$baseUrl/api/v0/catalog/schools", false), true),
                'observationsByProfessor' => $this->observationsBy("O.personId = :personId AND O.finish IS NOT NULL ", array('personId' => $session->get('personIdS')) )
            ));
        }
    }

    /**
     * @param $profileS
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setProfile($profileS){
        $profileJson = json_decode($profileS);
        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    /**
     * @return mixed
     */
    private function observationsBy($where, $setParameters){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $oByC = $qb->select("O.observationId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, O.gradeId, O.groupId, Cp.nameProgram, O.start, O.finish")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','O.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','O.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','O.programId = Cp.programId')
            ->where($where)
            ->setParameters($setParameters)
            ->orderBy( 'O.observationId')
            ->getQuery()
            ->getResult();

        $nivel = array('K' => 'Kinder', 'P' => 'Primaria', 'S' => 'Secundaria', 'B' => 'Bachillerato');
        $observationsByCoach = array();
        foreach ($oByC as $row){
            $row['nivel'] = strtoupper($row['gradeId'][1]);
            $row['nivelCompleto'] = $nivel[strtoupper($row['gradeId'][1])];
            $row['grado'] = $row['gradeId'][0].'°';
            array_push($observationsByCoach, $row);
        }
        return $observationsByCoach;
    }

}
