<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Observation;
use UNO\EvaluacionesBundle\Entity\ObservationAnswer;
use UNO\EvaluacionesBundle\Entity\ObservationAnswerHistory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UNO\EvaluacionesBundle\Controller\FileUpload\Document;

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

        $existNotFinish = $this->validObservationExist('O.gradeId = :gradeId AND O.groupId = :groupId AND O.programId = :programId', array(
            GRADEID_OB => $request->get(GRADEID_OB),
            GROUPID_OB => $request->get(GROUPID_OB),
            PROGRAMID_OB => (int)$request->get(PROGRAMID_OB)
        ));

        if(!$existNotFinish){
            $result = $this->addQuery(array(
                SURVEYID_OB => 19,
                GRADEID_OB => $request->get(GRADEID_OB),
                GROUPID_OB => $request->get(GROUPID_OB),
                PROGRAMID_OB => (int)$request->get(PROGRAMID_OB),
                PERSONID_OB => (int)$request->get(PERSONID_OB),
                SCHOOLID_OB => (int)$request->get(SCHOOLID_OB),
                COACHID_OB => (int)$request->get(COACHID_OB),
                START_OB => new \DateTime()
            ));
            #-----envia la respuesta en JSON-----#
            return new JsonResponse(array('id' => $result[MESSAGE_OB]), $result[STATUS_OB]);

        }else{
            #-----envia la respuesta en JSON-----#
            return new JsonResponse(array('error' => 'ObservationExist'), 409);
        }
    }

    private function validObservationExist($where, $parameters){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("O.observationId")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->where($where)
            ->andWhere('O.finish is null')
            ->setParameters($parameters)
            ->getQuery()
            ->getResult();
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
            ->where('QS.surveySurveyid = 19')
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

    /**
     * @Route("/observation/saveAnswer")
     * @Method({"POST"})
     */
    public function saveAnswerAction(Request $request){
        $session = $request->getSession();
        $session->start();
        
        $post['questionId'] = (int)$request->request->get('questionId');
        $post['personId'] = (int)$session->get('personIdS');
        $post['observationId'] = (int)$request->request->get('observationId');
        $post['answer'] = (int)$request->request->get('answer');
        $post['comment'] = $request->request->get('comment');

        $result = $this->validSaveAnswer($post);


        /*
        $result = $this->getResQueryAdd($answerHistory);
        */
        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function validSaveAnswer($post){
        $where = array_slice($post, 0, 3);
        $em = $this->getDoctrine()->getManager();
        $ObservationAnswer = $em->getRepository('UNOEvaluacionesBundle:ObservationAnswer')->findOneBy($where);

        if($ObservationAnswer){
            $post['observationAnswerId'] = $ObservationAnswer->getObservationAnswerId();
            #guarda en historico
            $history = $post;
            $history['answer'] = $ObservationAnswer->getAnswer();
            $history['comment'] = $ObservationAnswer->getComment();
            $history['dateRecord'] = $ObservationAnswer->getDateRecord();

            if($this->createAnswerHistoryQuery($history)){
                #actualiza
                $ObservationAnswer->setAnswer($post['answer']);
                $ObservationAnswer->setComment($post['comment']);
                $ObservationAnswer->setDateRecord(new \DateTime());
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
            $ObservationAnswer = new ObservationAnswer();
            $ObservationAnswer->setQuestionId($post['questionId']);
            $ObservationAnswer->setAnswer($post['answer']);
            $ObservationAnswer->setComment($post['comment']);
            $ObservationAnswer->setPersonId($post['personId']);
            $ObservationAnswer->setDateRecord(new \DateTime());
            $ObservationAnswer->setObservationId($post['observationId']);
            
            $em->persist($ObservationAnswer);
            $em->flush();
            return array('observationAnswerId' => $ObservationAnswer->getObservationAnswerId());
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }
    }

    private function createAnswerHistoryQuery($history){

        $em = $this->getDoctrine()->getManager();
        try{
            $ObservationAnswerHistory = new ObservationAnswerHistory();
            $ObservationAnswerHistory->setObservationAnswerId($history['questionId']);
            $ObservationAnswerHistory->setQuestionId($history['questionId']);
            $ObservationAnswerHistory->setAnswer($history['answer']);
            $ObservationAnswerHistory->setComment($history['comment']);
            $ObservationAnswerHistory->setPersonId($history['personId']);
            $ObservationAnswerHistory->setDateRecord($history['dateRecord']);
            $ObservationAnswerHistory->setObservationId($history['observationId']);

            $em->persist($ObservationAnswerHistory);
            $em->flush();
            return true;
        } catch(\Exception $e){
            print_r($e->getMessage());
            return false;
        }

    }


    /**
     * @Route("/observation/saveImg")
     * @Method({"POST"})
     */
    public function saveImgAction(Request $request){
        $session = $request->getSession();
        $session->start();

        $img = $request->files->get('image');
        print_r($img);
        if( ($img instanceof UploadedFile) && ($img->getError() == '0') ){
            $request = array(
                'originalName' => $img->getClientOriginalName(),
                'mimeType' => $img->getMimeType(),
                'pathName' => $img->getPathname(),
                'realPath' => $img->getRealPath()
            );

            $nameArray = explode('.',$request['originalName']);
            $fileType = $nameArray[sizeof($nameArray)-1];
            $validFileTypes = array('jpg','jpeg','bmg','png');
            //print_r($root = $this->get('kernel')->getRootDir()."/../www");
            if(in_array(strtolower($fileType), $validFileTypes)){
                $document = new Document();
                $document->setFile($img);
                $document->setUploadDirectory('public/assets/images/observation/uploads');
                $relativePath = date('Y-m', filemtime($img->getPath()));
                $document->setUploadHash('ghfashgsa.'.strtolower($fileType));
                $document->processFile();
                $uploadUrl = $document->getUploadDirectory(). DIRECTORY_SEPARATOR. $img->getBasename();
                $request = $uploadUrl;
            }
        }else{
            $request = array('message' => 'error file');
        }

        return new JsonResponse($request, 200);
    }

}