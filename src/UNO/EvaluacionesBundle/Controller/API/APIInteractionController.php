<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Interaction;
use UNO\EvaluacionesBundle\Entity\InteractionAnswer;
use UNO\EvaluacionesBundle\Entity\InteractionAnswerHistory;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 08/09/16
 * Time: 1:05 PM
 */

define('GRADEID_I','gradeId');
define('GROUPID_I','groupId');
define('PROGRAMID_I','programId');
define('PERSONID_I','personId');
define('SCHOOLID_I','schoolId');
define('COACHID_I','coachId');
define('START_I','start');
define('FINISH_I','finish');
define('STATUS_I','status');
define('MESSAGE_I','message');


/**
 * @Route("/api/v0")
 *
 */
class APIInteractionController extends Controller{


    /**
     * @Route("/interactions")
     * @Method({"GET"})
     */
    public function interactionsAction(){

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

        return $qb->select("I.interactionId, I.surveyId, I.gradeId, I.groupId, I.programId, I.personId, I.schoolId, I.coachId, I.start, I.finish")
            ->from('UNOEvaluacionesBundle:Interaction','I')
            ->orderBy( 'I.personId')
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/interaction/finish")
     * @Method({"POST"})
     */
    public function finishInteractionAction(Request $request){
        $interactionId = $request->request->get('interactionId');
        if($interactionId){
            //update
            $em = $this->getDoctrine()->getManager();
            $Interaction = $em->getRepository('UNOEvaluacionesBundle:Interaction')->findOneBy(array('interactionId'=> $interactionId));

            if($Interaction) {
                $Interaction->setFinish(new \DateTime());
                $em->flush();
                return new JsonResponse(array('status' => Utils::http_response_code(200)),200);
            }else{
                return new JsonResponse(array('status' => Utils::http_response_code(404)),404);
            }
        }else{
            return new JsonResponse(array('status' => Utils::http_response_code(409)),409);
        }

    }

    /**
     * @Route("/interactionsByCoach/{coachId}")
     * @Method({"GET"})
     */
    public function interactionsByCoachAction($coachId){

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

        return $qb->select("I.interactionId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, I.gradeId, I.groupId, Cp.nameProgram, I.start, I.finish")
            ->from('UNOEvaluacionesBundle:Interaction','I')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','I.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','I.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','I.programId = Cp.programId')
            ->where('I.coachId = :coachId')
            ->setParameter('coachId', $coachId)
            ->orderBy( 'I.interactionId')
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/interaction")
     * @Method({"POST"})
     */
    public function interactionAction(Request $request){

        $existNotFinish = $this->validInteractionExist('I.gradeId = :gradeId AND I.groupId = :groupId AND I.programId = :programId', array(
            GRADEID_I => $request->get(GRADEID_I),
            GROUPID_I => $request->get(GROUPID_I),
            PROGRAMID_I => (int)$request->get(PROGRAMID_I)
        ));

	if(!$existNotFinish){
            $result = $this->addQuery(array(
                GRADEID_I => $request->get(GRADEID_I),
                GROUPID_I => $request->get(GROUPID_I),
                PROGRAMID_I => (int)$request->get(PROGRAMID_I),
                PERSONID_I => (int)$request->get(PERSONID_I),
                SCHOOLID_I => (int)$request->get(SCHOOLID_I),
                COACHID_I => (int)$request->get(COACHID_I),
                START_I => new \DateTime()
            ));
            #-----envia la respuesta en JSON-----#
            return new JsonResponse(array('id' => $result[MESSAGE_I]), $result[STATUS_I]);

        }else{
            #-----envia la respuesta en JSON-----#
            return new JsonResponse(array('error' => 'InteractionExist', 'coach' => $existNotFinish[0]['coach']), 409);
        }
    }

    private function validInteractionExist($where, $parameters){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("I.interactionId, concat(P.name,' ',P.surname) as coach")
            ->from('UNOEvaluacionesBundle:Interaction','I')
	    ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','I.coachId = P.personid')
            ->where($where)
            ->andWhere('I.finish is null')
            ->setParameters($parameters)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $post
     * @return mixed
     */
    private function addQuery($post){

        $interaction = new Interaction();

        $interaction->setGradeId($post[GRADEID_I]);
        $interaction->setGroupId($post[GROUPID_I]);
        $interaction->setProgramId($post[PROGRAMID_I]);
        $interaction->setPersonId($post[PERSONID_I]);
        $interaction->setSchoolId($post[SCHOOLID_I]);
        $interaction->setCoachId($post[COACHID_I]);
        $interaction->setStart($post[START_I]);

        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($interaction);
        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return array(
            MESSAGE_I => $interaction->getInteractionId(),
            STATUS_I => 200
        );
    }

    /**
     * @Route("/interactionQuestion")
     * @Method({"GET"})
     */
    public function interactionQuestionAction(){

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

    /**
     * @Route("/interaction/saveAnswer")
     * @Method({"POST"})
     */
    public function saveAnswerAction(Request $request){
        $session = $request->getSession();
        $session->start();
        
        $post['questionId'] = (int)$request->request->get('questionId');
        $post['personId'] = (int)$session->get('personIdS');
        $post['interactionId'] = (int)$request->request->get('interactionId');
        $post['answer'] = (int)$request->request->get('answer');
        $post['comment'] = $request->request->get('comment');

        $result = $this->validSaveAnswer($post);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function validSaveAnswer($post){
        $where = array_slice($post, 0, 3);
        $em = $this->getDoctrine()->getManager();
        $InteractionAnswer = $em->getRepository('UNOEvaluacionesBundle:InteractionAnswer')->findOneBy($where);

        if($InteractionAnswer){
            $post['interactionAnswerId'] = $InteractionAnswer->getInteractionAnswerId();
            #guarda en historico
            $history = $post;
            $history['answer'] = $InteractionAnswer->getAnswer();
            $history['comment'] = $InteractionAnswer->getComment();
            $history['dateRecord'] = $InteractionAnswer->getDateRecord();

            if($this->createAnswerHistoryQuery($history)){
                #actualiza
                $InteractionAnswer->setAnswer($post['answer']);
                $InteractionAnswer->setComment($post['comment']);
                $InteractionAnswer->setDateRecord(new \DateTime());
                $em->flush();
                return array("message" => "updated");
            }
        }else{
            #crea
            return $this->createAnswerQuery($post);
        }
    }

    private function createAnswerQuery($post){
        $em = $this->getDoctrine()->getManager();
        try{
            $InteractionAnswer = new InteractionAnswer();
            $InteractionAnswer->setQuestionId($post['questionId']);
            $InteractionAnswer->setAnswer($post['answer']);
            $InteractionAnswer->setComment($post['comment']);
            $InteractionAnswer->setPersonId($post['personId']);
            $InteractionAnswer->setDateRecord(new \DateTime());
            $InteractionAnswer->setInteractionId($post['interactionId']);
            
            $em->persist($InteractionAnswer);
            $em->flush();
            return array('interactionAnswerId' => $InteractionAnswer->getInteractionAnswerId());
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    private function createAnswerHistoryQuery($history){

        $em = $this->getDoctrine()->getManager();
        try{
            $InteractionAnswerHistory = new InteractionAnswerHistory();
            $InteractionAnswerHistory->setInteractionAnswerId($history['questionId']);
            $InteractionAnswerHistory->setQuestionId($history['questionId']);
            $InteractionAnswerHistory->setAnswer($history['answer']);
            $InteractionAnswerHistory->setComment($history['comment']);
            $InteractionAnswerHistory->setPersonId($history['personId']);
            $InteractionAnswerHistory->setDateRecord($history['dateRecord']);
            $InteractionAnswerHistory->setInteractionId($history['interactionId']);

            $em->persist($InteractionAnswerHistory);
            $em->flush();
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }

    }

}
