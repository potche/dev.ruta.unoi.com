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
 * Date: 4/04/16
 * Time: 9:05 AM
 */

/**
 * @Route("/api/v0/statsCoaches")
 *
 */
class APIStatsCoachesController extends Controller{

    /**
     * @Route("/")
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
     * @Route("/zone/{zone}")
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
     * @Route("/day/{day}")
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
     * @Route("/zoneDay/{zone}/{day}")
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
            date_sub($date,date_interval_create_from_date_string("$day days"));
        }else{
            date_sub($date,date_interval_create_from_date_string("7 days"));
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_coaches = $qb->select("c.zona,
                                c.coordinador AS coordId,
                                CONCAT(p2.name, ' ', p2.surname) AS coordinador,
                                p.personid,
                                c.img,
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

    /**
     * @Route("/zoneCoaches")
     * @Method({"GET"})
     */
    public function zoneCoachesAction() {

        $zone= array();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_zoneCoaches = $qb->select("c.zona")
            ->from('UNOEvaluacionesBundle:Ccoach','c')
            ->where("c.zona != '' ")
            ->groupBy('c.zona')
            ->orderBy('c.zona', 'ASC')
            ->getQuery()
            ->getResult();

        if($_zoneCoaches){
            foreach($_zoneCoaches as $value){
                array_push($zone,$value['zona']);
            }
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($zone);

        return $response;
    }
}