<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use UNO\EvaluacionesBundle\Controller\API\APIUtils;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 8/12/15
 * Time: 3:05 PM
 */

/**
 * @Route("/api/v0/catalog")
 *
 */
class APICatalogoController extends Controller{

    /**
     * @Route("/surveys")
     *
     */
    public function surveysAction(){

        $result = $this->getResQuerySurvey(array('schoolId' => ''), 'PS.schoolid <> :schoolId');

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/survey/school/{schoolId}",
     * requirements={"schoolId" = "\d+"},
     * defaults={"schoolId" = null})
     * @Method({"GET"})
     */
    public function surveySchoolAction($schoolId){

        $result = $this->getResQuerySurvey(array('schoolId' => $schoolId), 'PS.schoolid = :schoolId');

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @param $parameters
     * @param $where
     * @return mixed
     */
    private function getResQuerySurvey($parameters, $where){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyList = $qb->select("S.surveyid, S.title")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile ','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'S.surveyid = SP.surveySurveyid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere($where)
            ->setParameters($parameters)
            ->groupBy('S.surveyid')
            ->orderBy( 'S.surveyid')
            ->getQuery()
            ->getResult();

        return($_surveyList);
    }

    /**
     * @Route("/schools")
     */
    public function schoolAction(){

        $result = $this->getResQuerySchool();

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * obtiene una lista con loas escuelas para poder filtrar (unicamente para Admin)
     * la almacena en el atributo $this->_schoolId
     */
    private function getResQuerySchool() {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_schoolId = $qb->select("trim(PS.schoolid) as schoolid, trim(S.school) as school")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:School','S', 'WITH', 'PS.schoolid = S.schoolid')
            ->groupBy('PS.schoolid')
            ->orderBy('PS.schoolid')
            ->getQuery()
            ->getResult();

        return $_schoolId;
    }
}