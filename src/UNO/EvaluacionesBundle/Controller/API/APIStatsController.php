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

    public function progressbysurveyAction(Request $request, $surveyid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,$surveyid));

        return $response;
    }

    public function progressbyschoolAction(Request $request, $schoolid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,null,null,$schoolid));

        return $response;
    }

    public function progressbylevelAction(Request $request, $levelid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,null,$levelid));

        return $response;
    }

    public function progressbyprofileAction(Request $request, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,$profileid));

        return $response;
    }

    public function progressbypersonAction(Request $request, $personid){

        $response = new JsonResponse();
        $response->setData($this->getAll($personid));

        return $response;
    }

    public function progressbylevelprofileAction(Request $request, $levelid, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,$profileid,$levelid));

        return $response;
    }

    public function progressbyschoolroleAction(Request $request, $schoolid, $levelid, $profileid ){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,$profileid,$levelid, $schoolid));

        return $response;
    }

    public function progressbyschoollevelAction(Request $request, $schoolid, $levelid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,null,$levelid,$schoolid));

        return $response;

    }

    public function progressbyschoolprofileAction(Request $request, $schoolid, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getAll(null,null,$profileid,null,$schoolid));

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
            ->groupBy('id, persona, estatus')
            ->getQuery()
            ->getResult();

        return $all ? $this->buildResponse($all) : APIUtils::getErrorResponse('404');
    }

    private function buildResponse($all){

        $esperadas = count($all);
        $respondidas = array_count_values(array_column($all,'estatus'))['4'];
        $porc_cumplimiento = ($esperadas > 0 ? round(($respondidas * 100 ) / $esperadas,2) : 0);
        $porc_pendiente = 100 - $porc_cumplimiento;

        $response = array(
            'global'=>array(
                'esperadas' => $esperadas,
                'respondidas' => $respondidas,
                'porc_cumplimiento' => $porc_cumplimiento,
                'porc_pendiente' => $porc_pendiente,
            ),
            'bySurvey'=>array(),
        );

        foreach (array_unique(array_column($all,'id')) as $a) {

            if(!isset($response['bySurvey'][$a])){

                $esperadas = count(array_filter($all, function($ar) use($a){ return ($ar['id'] == $a); }));
                $respondidas = count(array_filter($all, function($ar) use($a){ return ($ar['estatus'] == '4' AND $ar['id'] == $a); },ARRAY_FILTER_USE_BOTH));
                $porc_cumplimiento = ($esperadas > 0 ? round(($respondidas * 100 ) / $esperadas,2) : 0);
                $porc_pendiente = 100 - $porc_cumplimiento;

                $response['bySurvey'][$a] = array(
                    'esperadas' => $esperadas,
                    'respondidas' => $respondidas,
                    'porc_cumplimiento' => $porc_cumplimiento,
                    'porc_pendiente' => $porc_pendiente,
                    );
            }
        }

        return $response;
    }
}