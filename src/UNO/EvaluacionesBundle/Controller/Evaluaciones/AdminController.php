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

        $data = $this->getSurveyList();


        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig');



    }

    private function getSurveyList(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $surveyList = $qb->select(
            "su.surveyid,su.title,
        '' as perfiles,
        '' as esperadas,
        '' as respondidas,
        '' as avance,
        su.creationdate,
        su.closingdate"
        )->from('UNOEvaluacionesBundle:Survey', 'su')
            ->orderBy('su.surveyid')
            ->getQuery()
            ->getResult();

        return $surveyList;
    }

    private function fillDataArray($data){

        foreach ($data as $row){

            


        }
    }

    private function getStatsBySurvey($data){

        /*$em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $answeredBySurvey= $qb->select('sxp.surveySurveyid')
            ->
            ->from('UNOEvaluacionesBundle:Surveyxprofile', 'sxp')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile', 'sxp', 'WITH', 'sxp.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr','WITH','pr.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','sl','WITH','sl.schoollevelid = sxp.schoollevelid')
            ->orderBy('su.surveyid')
            ->getQuery()
            ->getResult();*/
    }

}