<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Observation;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 23/05/16
 * Time: 1:05 PM
 */

define('SURVEYID_OB','surveyId');
define('GRADEID_OB','gradeId');
define('GROUPID_OB','groupId');
define('PROGRAMID_OB','programId');
define('PERSONID_OB','personId');
define('SCHOOLID_OB','schoolId');
define('COACHID_OB','coachId');
define('START_OB','start');
define('FINISH_OB','finish');
define('STATUS_OB','status');
define('MESSAGE_OB','message');


/**
 * @Route("/api/v0")
 *
 */
class APIObservationController extends Controller{

    /**
     * @Route("/observations")
     * @Method({"GET"})
     */
    public function observationsAction(){

        $result = $this->getResQuery(null, null);

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
    private function getResQuery($parameters = null, $where = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("O.observationId, O.surveyId, O.gradeId, O.groupId, O.programId, O.personId, O.schoolId, O.coachId, O.start, O.finish")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->orderBy( 'O.personId')
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/observation")
     * @Method({"POST"})
     */
    public function observationAction(Request $request){
        
        $post = array(
            SURVEYID_OB => 19,
            GRADEID_OB => $request->get(GRADEID_OB),
            GROUPID_OB => $request->get(GROUPID_OB),
            PROGRAMID_OB => (int)$request->get(PROGRAMID_OB),
            PERSONID_OB => (int)$request->get(PERSONID_OB),
            SCHOOLID_OB => (int)$request->get(SCHOOLID_OB),
            COACHID_OB => (int)$request->get(COACHID_OB),
            START_OB => new \DateTime()
        );

        $result = $this->addQuery($post);

        #-----envia la respuesta en JSON-----#
        return new JsonResponse(array(MESSAGE_OB => $result[MESSAGE_OB]), $result[STATUS_OB]);

    }

    /**
     * @param $post
     * @return mixed
     */
    private function addQuery($post){

        $observation = new Observation();

        $observation->setSurveyId($post[SURVEYID_OB]);
        $observation->setGradeId($post[GRADEID_OB]);
        $observation->setGroupId($post[GROUPID_OB]);
        $observation->setProgramId($post[PROGRAMID_OB]);
        $observation->setPersonId($post[PERSONID_OB]);
        $observation->setSchoolId($post[SCHOOLID_OB]);
        $observation->setCoachId($post[COACHID_OB]);
        $observation->setStart($post[START_OB]);

        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($observation);
        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return array(
            MESSAGE_OB => 'Saved new observation with id '.$observation->getObservationId(),
            STATUS_OB => 200
        );
    }

}