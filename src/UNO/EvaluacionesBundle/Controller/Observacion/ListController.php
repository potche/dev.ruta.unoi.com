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

        $baseUrl = "http://dev.ruta.unoi.com".$this->container->get('router')->getContext()->getBaseUrl();

        return $this->render('UNOEvaluacionesBundle:Observacion:index.html.twig', array(
                'schoolList' => json_decode(file_get_contents("$baseUrl/api/v0/catalog/schools", false), true),
                'observationsByCoach' => $this->observationsByCoach($session->get('personIdS'))
            ));

    }
    
    /**
     * @return mixed
     */
    private function observationsByCoach($coachId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $oByC = $qb->select("O.observationId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, O.gradeId, O.groupId, Cp.nameProgram, O.start, O.finish")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','O.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','O.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','O.programId = Cp.programId')
            ->where('O.coachId = :coachId')
            ->setParameter('coachId', $coachId)
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
