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

        $statsBySurvey = $this->getStatsBySurvey();

        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig');
    }

    private function getStatsBySurvey(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $expectedBySurvey = $qb->select("su.surveyid,'' as perfiles, '' as avance, su.creationdate, su.closingdate, count(distinct p.personid) as expectedNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->where('sxp.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->groupBy('sxp.surveySurveyid, perfiles, avance, su.creationdate, su.closingdate')
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

        foreach($expectedBySurvey as $expectedElem) {

            $avance = 0.0;
            foreach ($answeredBySurvey as $answeredElement) {

                if($expectedElem['surveyid'] == $answeredElement['surveyid']) {

                    $avance  = ($answeredElement['answeredNum'] * 100 ) / $expectedElem['expectedNum'];
                }
            }

            $expectedElem['avance'] = $avance;

            /**
             * ToDo: Query para obtener perfiles y niveles de evaluacion
             */
        }
        return $expectedBySurvey;
    }

}