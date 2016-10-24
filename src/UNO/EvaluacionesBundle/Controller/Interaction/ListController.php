<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 08/09/16
 * Time: 10:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Interaction;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Class ListController
 * @package UNO\EvaluacionesBundle\Controller\Interaction
 */
class ListController extends Controller{

    /**
     * @Route("/interaction")
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
        
        return $this->render('UNOEvaluacionesBundle:Interaction:index.html.twig', array(
                'schoolList' => json_decode(file_get_contents("$baseUrl/api/v0/catalog/schools", false), true),
                'interactionsByCoach' => $this->interactionsByCoach($session->get('personIdS'))
            ));

    }
    
    /**
     * @return mixed
     */
    private function interactionsByCoach($coachId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $oByC = $qb->select("I.interactionId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, I.gradeId, I.groupId, Cp.nameProgram, I.start, I.finish")
            ->from('UNOEvaluacionesBundle:Interaction','I')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','I.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','I.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','I.programId = Cp.programId')
            ->where('I.coachId = :coachId')
            ->setParameter('coachId', $coachId)
            ->orderBy( 'I.interactionId')
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
