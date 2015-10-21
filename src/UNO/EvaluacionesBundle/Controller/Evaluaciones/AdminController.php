<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 13/10/15
 * Time: 11:35 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function indexAction(Request $request){

        $stats = $this->getStats();
        $surveys = $this->getSurveysWithProfiles($stats);
        $stats_general =  $stats['general'];


        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig', array(
            'surveylist' => $surveys,
            'stats_general' => $stats_general,
        ));
    }

    private function getSurveysWithProfiles($stats){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $surveysWithProfiles = $qb->select("su.surveyid, su.title, su.creationdate, su.createdby, su.active, su.closingdate, pr.profile, sl.schoollevel")
            ->from('UNOEvaluacionesBundle:Survey', 'su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr', 'WITH','pr.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','sl', 'WITH','sl.schoollevelid = sxp.schoollevelid')
            ->where('sxp.surveySurveyid > 1')
            ->getQuery()
            ->getResult();

        $surveys = array_flip(array_unique(array_column($surveysWithProfiles,'surveyid')));

        foreach ($surveys as $surveyid => $survey) {

            $surveys[$surveyid] = array();
            $surveys[$surveyid]['profiles'] = array();

            foreach($surveysWithProfiles as $swp) {

                if($swp['surveyid'] == $surveyid && !array_key_exists('surveyid',$surveys[$surveyid])) {

                    $surveys[$surveyid]['id'] = $swp['surveyid'];
                    $surveys[$surveyid]['title'] = $swp['title'];
                    $surveys[$surveyid]['created'] = $swp['creationdate']->format('j/M/Y').' por: '.$swp['createdby'];
                    $surveys[$surveyid]['closingdate'] = $swp['closingdate']->format('j/M/Y \@ g:i a');
                    $surveys[$surveyid]['progress'] = $stats['bySurvey'][$surveyid]['avance'];
                    $surveys[$surveyid]['completed'] = $stats['bySurvey'][$surveyid]['respondido'];
                    $surveys[$surveyid]['expected'] = $stats['bySurvey'][$surveyid]['esperado'];
                    $surveys[$surveyid]['active'] = $swp['active'];
                    array_push($surveys[$surveyid]['profiles'],$swp['profile'].' de '.$swp['schoollevel']);
                }
                elseif( $swp['surveyid'] == $surveyid && array_key_exists('surveyid',$surveys[$surveyid])){

                    array_push($surveys[$surveyid]['profiles'],$swp['profile'].' de '.$swp['schoollevel']);
                }
            }
        }
        return $surveys;
    }

    private function getStats(){

        $data = array();

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $expectedBySurvey = $qb->select("su.surveyid, count(distinct p.personid) as expectedNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->where('sxp.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->groupBy('sxp.surveySurveyid')
            ->getQuery()
            ->getResult();

        $qb = $em->createQueryBuilder();
        $answeredBySurvey= $qb->select("su.surveyid, count(distinct ps.personid) as answeredNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','log','WITH','ps.personid = log.personPersonid AND sxp.surveySurveyid = log.surveySurveyid')
            ->where('log.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->andWhere('log.actionaction = 5')
            ->groupBy('sxp.surveySurveyid')
            ->getQuery()
            ->getResult();

        foreach($expectedBySurvey as $k => $expectedElem) {

            $avance = 0.0;
            $esperado = 0;
            $respondido = 0;

            foreach ($answeredBySurvey as $answeredElement) {

                if($expectedElem['surveyid'] == $answeredElement['surveyid']) {

                    $esperado = $expectedElem['expectedNum'];
                    $respondido = $answeredElement['answeredNum'];
                    $avance  = ($esperado > 0 ? round(($respondido * 100 ) / $esperado,2) : 0);
                }
            }
            $data['bySurvey'][$expectedElem['surveyid']]['avance'] = $avance;
            $data['bySurvey'][$expectedElem['surveyid']]['esperado'] = $esperado;
            $data['bySurvey'][$expectedElem['surveyid']]['respondido'] = $respondido;
        }

        $data['general'] = array();

        $esperadoGlobal = array_sum(array_column($expectedBySurvey,'expectedNum'));
        $respondidoGlobal = array_sum(array_column($answeredBySurvey,'answeredNum'));
        $avanceGlobal =  ($esperado > 0 ? round(($respondidoGlobal * 100) / $esperadoGlobal,2) : 0);

        array_push($data['general'],array(
            'name' => 'Completado',
            'y' => $avanceGlobal
            ));

        array_push($data['general'],array(
            'name' => 'Pendiente',
            'y' => 100 - $avanceGlobal
        ));

        return $data;
    }

}