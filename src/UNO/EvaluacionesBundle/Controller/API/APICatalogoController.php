<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Ccoach;

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

    /**
     * @Route("/coaches")
     * @Method({"GET"})
     * default 7 days
     */
    public function coachesAction(){

        $result = $this->getResQueryCoaches(null, null);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/coachesZone/{zone}")
     * @Method({"GET"})
     */
    public function coachesZonaAction(Request $request, $zone){
        if (preg_match('/^[a-zA-Z ]+$/', $zone)) {
            $result = $this->getResQueryCoaches($zone, null);

            #-----envia la respuesta en JSON-----#
            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }else{
            #-----envia la respuesta en JSON-----#
            $responseError = new JsonResponse();
            $responseError->setData(['status'=> 'error', 'message' => 'zone is not a valid format']);
            return $responseError;
        }

    }

    /**
     * @Route("/coachesDay/{day}")
     * @Method({"GET"})
     */
    public function coachesDayAction(Request $request, $day){
        if (preg_match('/^[0-9]+$/', $day)) {
            $result = $this->getResQueryCoaches(null, $day);

            #-----envia la respuesta en JSON-----#
            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }else{
            #-----envia la respuesta en JSON-----#
            $responseError = new JsonResponse();
            $responseError->setData(['status'=> 'error', 'message' => 'day is not a valid format']);
            return $responseError;
        }


    }

    /**
     * @Route("/coachesZoneDay/{zone}/{day}")
     * @Method({"GET"})
     */
    public function coachesZoneDayAction(Request $request, $zone, $day){


        if ( preg_match('/^[a-zA-Z ]+$/', $zone) && preg_match('/^[0-9]+$/', $day) ) {
            $result = $this->getResQueryCoaches($zone, $day);

            #-----envia la respuesta en JSON-----#
            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }else{
            #-----envia la respuesta en JSON-----#
            $responseError = new JsonResponse();
            $responseError->setData(['status'=> 'error', 'message' => 'zone or day is not a valid format']);
            return $responseError;
        }

    }

    /**
     * obtiene una lista con los ecoaches que han entrado de cada zona (unicamente para Admin)
     */
    private function getResQueryCoaches($zone, $day) {

        if($zone){
            $queryZone = " c.zona = '$zone' ";
        }else{
            $queryZone = " c.zona != '' ";
        }

        $date = new \DateTime('now');
        if($day){
            date_sub($date,date_interval_create_from_date_string($day+" days"));
        }else{
            date_sub($date,date_interval_create_from_date_string("21 days"));
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_coaches = $qb->select("c.zona,
                                c.coordinador AS coordId,
                                CONCAT(p2.name, ' ', p2.surname) AS coordinador,
                                p.personid,
                                CONCAT(p.name, ' ', p.surname) AS coach,
                                u.browser,
                                u.platform,
                                COUNT(u.startsession) AS numSession,
                                p.lastLogin")
            ->from('UNOEvaluacionesBundle:Ccoach','c')
            ->innerJoin('UNOEvaluacionesBundle:Person','p', 'WITH', 'p.user = c.user ')
            ->innerJoin('UNOEvaluacionesBundle:Person','p2', 'WITH', 'c.coordinador = p2.personid')
            ->innerJoin('UNOEvaluacionesBundle:Userhttpsession','u', 'WITH', 'p.personid = u.personid')
            ->where("u.startsession >= :date")
            ->andWhere($queryZone)
            ->setParameter('date', date_format($date,"Y-m-d"))
            ->groupBy('p.personid')
            ->orderBy('c.zona', 'ASC')
            ->addOrderBy('numSession', 'DESC')
            ->getQuery()
            ->getResult();

        return $_coaches;
    }
}