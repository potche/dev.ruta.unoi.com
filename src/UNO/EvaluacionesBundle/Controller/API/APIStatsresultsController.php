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
        $response->setData($this->getByParams($schoolid));
        return $response;
    }

    public function statsbypersonAction(Request $request, $personid){

        $response = new JsonResponse();
        $response->setData($this->getByParams(null,null,null,null,$personid));
        return $response;

    }

    public function statsbysurveyschoolAction(Request $request, $surveyid, $schoolid){

        $response = new JsonResponse();
        $response->setData($this->getByParams($schoolid,$surveyid));
        return $response;

    }

    public function statsbysurveypersonAction(Request $request, $surveyid, $personid){

        $response = new JsonResponse();
        $response->setData($this->getByParams(null,$surveyid,null,null,$personid));
        return $response;
    }

    public function statsbyschoollevelAction(Request $request, $schoolid, $levelid){

        $response = new JsonResponse();
        $response->setData($this->getByParams($schoolid,null,null,$levelid));
        return $response;
    }

    public function statsbyschoolprofileAction(Request $request, $schoolid, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getByParams($schoolid,null,$profileid));
        return $response;
    }

    public function statsbyschoolroleAction(Request $request, $schoolid, $levelid, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getByParams($schoolid,null,$profileid,$levelid));
        return $response;
    }

    public function getAll($surveyid = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $condition = 'su.surveyid IS NOT NULL';
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

    private function getByParams($schoolid = null, $surveyid = null, $profileid = null, $levelid=null, $personid = null ){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $condition = 'su.surveyid IS NOT NULL';
        $condition = $schoolid != null ? $condition.' AND ps.schoolid = '.$schoolid : $condition;
        $condition = $surveyid != null ? $condition.' AND su.surveyid = '.$surveyid : $condition;
        $condition = $profileid != null ? $condition.' AND ps.profileid = '.$profileid : $condition;
        $condition = $levelid != null ? $condition.' AND ps.schoollevelid = '.$levelid : $condition;
        $condition = $personid != null ? $condition.' AND ps.personid = '.$personid : $condition;

        $byParams = $qb->select("su.surveyid as id, su.title as titulo, ps.personid as persona, CONCAT(p.name,' ',p.surname) as nombre, COALESCE(a.idaction,0) as estatus, l.date as fecharespuesta, o.option as opcion, COUNT(DISTINCT(ans.answerid)) as resp")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l', 'WITH','l.personPersonid = ps.personid AND l.surveySurveyid = su.surveyid')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','l.actionaction = a.idaction')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','su.surveyid = qxs.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','ans','WITH','ans.optionxquestion = oxq.optionxquestionId AND ans.personPersonid = ps.personid')
            ->where($condition)
            ->groupBy('persona, nombre, id, titulo, estatus, fecharespuesta, oxq.optionOptionid')
            ->getQuery()
            ->getResult();

        return $byParams ? $this->parseByParams($byParams) : APIUtils::getErrorResponse('404');
    }

    private function parseGeneral($all){

        $options = array_unique(array_column($all,'opcion'));
        $ids = array_unique(array_column($all,'id'));

        $statsAll = array(
            'global' => $this->getGlobal($all,$options),
            'bySurvey' => array(),
        );

        foreach($options as $o){

            $r = array_filter($all, function($ar) use($o){ return ($ar['opcion'] == $o); });

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


    private function parseByParams($byParams){

        $options = array_unique(array_column($byParams,'opcion'));
        $persons = array_unique(array_column($byParams,'persona'));

        $statsByParams = array(
            'global' => $this->getGlobal($byParams,$options),
            'byPerson' => array()
        );

        foreach ($persons as $p){

            $byperson = array_filter($byParams, function($ar) use($p){ return ($ar['persona'] == $p); });
            $evals = array_unique(array_column($byperson,'id'));
            $pi = array_push($statsByParams['byPerson'],array(

                'persona' => array_column($byperson,'persona')[0],
                'nombre' => array_column($byperson,'nombre')[0],
                'evaluaciones' => array(),
            ));

            foreach ($evals as $e) {

                $byopts = array_filter($byperson, function($ar) use($e){ return ($ar['id'] == $e); });

                $ei = array_push($statsByParams['byPerson'][$pi-1]['evaluaciones'],array(
                    'id' => $e,
                    'titulo' => array_unique(array_column($byopts,'titulo'))[0],
                    'estatus' => array_unique(array_column($byopts,'estatus'))[0],
                    'fecharespuesta' => array_unique(array_column($byopts,'fecharespuesta'),SORT_REGULAR)[0],
                    'opciones' => array()
                ));

                foreach ($options as $o){

                    $opcionfila =  array_filter($byopts, function($ar) use($o){ return ($ar['opcion'] == $o); });
                    array_push($statsByParams['byPerson'][$pi-1]['evaluaciones'][$ei-1]['opciones'],array(
                        'name' => array_unique(array_column($opcionfila,'opcion'))[0],
                        'y' => intval(array_unique(array_column($opcionfila,'resp'))[0]),
                    ));
                }
            }
        }
        return $statsByParams;
    }

    private function getGlobal($query, $options){

        $global = array();

        foreach($options as $o){

            $r = array_filter($query, function($ar) use($o){ return ($ar['opcion'] == $o ); });
            $suma = array_sum(array_column($r,'resp'));

            array_push($global,array(
                'name' => $o,
                'y' => $suma
            ));
        }
        return $global;
    }
}