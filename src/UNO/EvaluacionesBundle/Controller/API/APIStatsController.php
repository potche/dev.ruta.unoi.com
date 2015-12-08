<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 08/12/15
 * Time: 10:11 AM
 */

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class APIStatsController extends Controller
{

    public function progressAction(Request $request){

        $response = new JsonResponse();
        $response->setData($this->getAll());

        return $response;

    }


    private function getAll($personid = null, $surveyid = null, $profileid = null, $level = null, $school = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $condition = "su.surveyid > 1";
        $condition = $personid != null ? $condition.' AND p.personid = '.$personid : $condition;
        $condition = $surveyid != null ? $condition.' AND su.surveyid = '.$surveyid : $condition;
        $condition = $profileid != null ? $condition.' AND ps.profileProfileid = '.$profileid : $condition;
        $condition = $level != null ? $condition.' AND ps.schoollevelid = '.$level : $condition;
        $condition = $school != null ? $condition.' AND ps.schoolid = '.$school : $condition;

        $all = $qb->select("su.surveyid as id, p.personid as persona, COALESCE(a.idaction,0) as estatus")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l', 'WITH','l.personPersonid = p.personid AND l.surveySurveyid = su.surveyid')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','l.actionaction = a.idaction')
            ->where($condition)
            ->groupBy('id, persona, a.idaction')
            ->getQuery()
            ->getResult();

        return $all;
    }

}