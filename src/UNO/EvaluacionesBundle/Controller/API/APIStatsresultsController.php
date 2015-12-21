<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 09/12/15
 * Time: 12:34 PM
 */

namespace UNO\EvaluacionesBundle\Controller\API;

use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Surveyxprofile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class APIStatsresultsController extends Controller {

    public function allstatsAction(Request $request){


        $response = new JsonResponse();
        $response->setData($this->getAll());

        return $response;
    }

    public function statsbysurveyAction(Request $request, $surveyid){

        $response = new JsonResponse();
        $response->setData($this->getAll($surveyid));

        return $response;
    }

    public function statsbyschoolAction(Request $request, $schoolid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbypersonAction(Request $request, $personid){

        $response = new JsonResponse();
        $response->setData();

        return $response;

    }

    public function statsbysurveyschoolAction(Request $request, $surveyid, $schoolid){

        $response = new JsonResponse();
        $response->setData();

        return $response;

    }

    public function statsbysurveypersonAction(Request $request, $surveyid, $personid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoollevelAction(Request $request, $schoolid, $levelid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoolprofileAction(Request $request, $schoolid, $profileid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoolroleAction(Request $request, $schoolid, $levelid, $profileid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function getAll($surveyid = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $condition = 'su.surveyid > 1';
        $condition = $surveyid != null ? $condition.' AND su.surveyid = '.$surveyid : $condition;

        $all = $qb->select("su.surveyid as id, su.title as titulo, o.option as opcion, COUNT(ans.answerid) as resp")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','su.surveyid = qxs.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','ans','WITH','ans.optionxquestion = oxq.optionxquestionId')
            ->where($condition)
            ->groupBy('id, titulo, oxq.optionOptionid')
            ->getQuery()
            ->getResult();

        return $all ? $this->parseGeneral($all) : APIUtils::getErrorResponse('404');
    }

    private function parseGeneral($all){

        $statsAll = array(
            'global' => array(),
            'bySurvey' => array(),
        );

        $options = array_unique(array_column($all,'opcion'));
        $ids = array_unique(array_column($all,'id'));

        foreach($options as $o){

            $r = array_filter($all, function($ar) use($o){ return ($ar['opcion'] == $o); });
            $suma = array_sum(array_column($r,'resp'));

            array_push($statsAll['global'],array(
                'name' => $o,
                'y' => $suma
            ));

            foreach ($ids as $id) {

                $surveys = array_filter($r, function($ar) use($id){ return ($ar['id'] == $id); });

                if(!isset($statsAll['bySurvey'][$id])){

                    $statsAll['bySurvey'][$id] = array(
                        'titulo' => array_column($surveys,'titulo')[0],
                        'opciones'=>array()
                    );
                }

                array_push($statsAll['bySurvey'][$id]['opciones'],array(
                   'name' => $o,
                    'y'=> intval(array_column($surveys,'resp')[0])
                ));
            }
        }
        return $statsAll;
    }

    private function getByParams($schoolid = null, $surveyid = null, $profileid = null, $levelid=null, $personid = null ){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $condition = 'su.surveyid > 1';
        $condition = $schoolid != null ? $condition.' AND ps.schoolid = '.$schoolid : $condition;
        $condition = $surveyid != null ? $condition.' AND su.surveyid = '.$surveyid : $condition;
        $condition = $profileid != null ? $condition.' AND ps.profileid = '.$profileid : $condition;
        $condition = $levelid != null ? $condition.' AND ps.schoollevelid = '.$levelid : $condition;
        $condition = $personid != null ? $condition.' AND ps.personid = '.$personid : $condition;

        /*$byParameters = $qb->select("su.surveyid as id, su.title as titulo, ps.personid as persona, CONCAT(p.name,' ',p.surname) as nombre, COALESCE(a.idaction,0) as estatus, o.option as opcion, COUNT(DISTINCT(ans.answerid)) as resp")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.surveySurveyid = su.surveyid AND l.personPersonid = ps.personid')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','a.idaction = l.actionaction = a.idaction')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','su.surveyid = qxs.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','ans','WITH','ans.optionxquestion = oxq.optionxquestionId')
        */









    }







}