<?php
/**
 * Created by PhpStorm.
 * User: jbravob
 * Date: 16/12/15
 * Time: 1:44 PM
 */

namespace UNO\EvaluacionesBundle\Controller\API;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Date;

class APINotificationsController extends Controller
{

    public function newsurveysAction(Request $request, $daysago){

        $response = new JsonResponse();
        $response->setData($this->getNewSurveys($daysago));
        return $response;
    }

    protected function getNewSurveys($daysago){

        if($daysago > 7){

            return APIUtils::getErrorResponse('400');
        }

        $date1 = date('Y-m-d',mktime(date("H"), date("i"), date("s"), date("m"), date("d")-$daysago, date("Y")));

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $all = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->where($qb->expr()->between('su.creationdate',':daysago','CURRENT_TIMESTAMP()'))
            ->groupBy('id, persona')
            ->setParameter('daysago',$date1)
            ->getQuery()
            ->getResult();

        return $all ? $this->parseArray($all) : APIUtils::getErrorResponse('404');
    }

    private function parseArray($all){

        $parsedArray = array();
        $users = array_unique(array_column($all,'persona'));

        foreach ($users as $u) {

            $byperson = array_filter($all, function($ar) use($u){ return ($ar['persona'] == $u); });
            array_push($parsedArray,array(
                'Persona'=> array_column($byperson,'persona')[0],
                'Nombre' => array_column($byperson,'nombre')[0],
                'Email' => array_column($byperson,'email')[0],
                'NewSurveys' => $byperson
            ));
        }
        return $parsedArray;
    }

}