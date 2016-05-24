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

        $result = $this->getResQuery();

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getResQuery(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("O.observationId, O.surveyId, O.gradeId, O.groupId, O.programId, O.personId, O.schoolId, O.coachId, O.start, O.finish")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->orderBy( 'O.personId')
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/observationsByCoach/{coachId}")
     * @Method({"GET"})
     */
    public function observationsByCoachAction($coachId){

        $result = $this->getResQueryObyC($coachId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getResQueryObyC($coachId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("O.observationId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, O.gradeId, O.groupId, Cp.nameProgram, O.start")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','O.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','O.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','O.programId = Cp.programId')
            ->where('O.coachId = :coachId')
            ->setParameter('coachId', $coachId)
            ->orderBy( 'O.observationId')
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
        return new JsonResponse(array('id' => $result[MESSAGE_OB]), $result[STATUS_OB]);

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
            MESSAGE_OB => $observation->getObservationId(),
            STATUS_OB => 200
        );
    }

    /**
     * @Route("/observationQuestion")
     * @Method({"GET"})
     */
    public function observationQuestionAction(){

        $result = $this->getResQueryOQ(null, null);

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
    private function getResQueryOQ($parameters = null, $where = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $obQ = $qb->select("Q.questionid, QS.order, Q.question, Sub.subcategory")
            ->from('UNOEvaluacionesBundle:Questionxsurvey','QS')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q','WITH','QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub','WITH','Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->andWhere('QS.surveySurveyid = 19')
            //->setParameters($parameters)
            ->orderBy( 'QS.order')
            ->getQuery()
            ->getResult();

        $questionByCategory = array();
        $subcategorys = array_unique(array_column($obQ, 'subcategory'));

        foreach ($subcategorys as $subcategory){
            $questions = array();
            foreach ($obQ as $question){
                if($question['subcategory'] === $subcategory){
                    array_push($questions, array('order' => $question['order'], 'question' => $question['question'], 'questionId' => $question['questionid']));
                }
            }
            array_push($questionByCategory, array('category' => $subcategory, 'questions' => $questions));
        }

        return $questionByCategory;
    }
}