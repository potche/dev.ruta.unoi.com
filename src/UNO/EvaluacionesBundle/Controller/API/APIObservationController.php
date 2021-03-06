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
use UNO\EvaluacionesBundle\Entity\ObservationGallery;
use UNO\EvaluacionesBundle\Entity\ObservationDisposition;
use UNO\EvaluacionesBundle\Entity\ObservationActivity;
use UNO\EvaluacionesBundle\Entity\ObservationAspects;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
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
define('BUCKET','pre-staticmx.unoi.com');
define('KEYS3','ruta/observacion/');
define('DIRS3','https://pre-staticmx.unoi.com/ruta/observacion/');


/**
 * @Route("/api/v0")
 *
 */
class APIObservationController extends Controller{

    private $_obsId;
    private $_type;
    private $_observationId;
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
     * @Route("/observation/finish")
     * @Method({"POST"})
     */
    public function finishObservationAction(Request $request){
        $observationId = $request->request->get('observationId');
        if($observationId){
            //update
            $em = $this->getDoctrine()->getManager();
            $Observation = $em->getRepository('UNOEvaluacionesBundle:Observation')->findOneBy(array('observationId'=> $observationId));

            if($Observation) {
                $Observation->setFinish(new \DateTime());
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

        return $qb->select("O.observationId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, O.gradeId, O.groupId, Cp.nameProgram, O.start, O.finish")
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
            return new JsonResponse(array('error' => 'ObservationExist', 'coach' => $existNotFinish[0]['coach']), 409);
        }
    }

    private function validObservationExist($where, $parameters){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("O.observationId, concat(P.name,' ',P.surname) as coach")
            ->from('UNOEvaluacionesBundle:Observation','O')
	    ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','O.coachId = P.personid')
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

        $this->addQueryAspects($observation->getObservationId());

        return array(
            MESSAGE_OB => $observation->getObservationId(),
            STATUS_OB => 200
        );
    }

    /**
     *
     */
    private function addQueryAspects($observationId){

        $observationAspects = new ObservationAspects();

        $observationAspects->setObservationId($observationId);

        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($observationAspects);
        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return true;
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
        if($request->request->get('obsIdA')){
            $this->_observationId = $request->request->get('obsIdA');
            $this->_type = 'A';
            $this->_obsId = $this->_observationId.'-A';
            $img = $request->files->get('imageA');
        }else if($request->request->get('obsIdB')){
            $this->_observationId = $request->request->get('obsIdB');
            $this->_type = 'B';
            $this->_obsId = $this->_observationId.'-B';
            $img = $request->files->get('imageB');
        }

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
                $relativePath = md5( date('Y-m-d-H-i-s'). $this->_obsId ).'.'.strtolower($fileType);

                if($this->createOrUpdateGalleryQuery($img, $relativePath, $request['mimeType'])){
                    return new JsonResponse(array('status' => Utils::http_response_code(200)),200);
                }
            }else{
                return new JsonResponse(array('status' => Utils::http_response_code(409)),409);
            }
        }else{
            return new JsonResponse(array('status' => Utils::http_response_code(400)),400);
        }

    }

    private function S3Put($client, $sourceFileName, $nameFile, $contentType){
        // Upload a publicly accessible file. The file size and type are determined by the SDK.
        try {
            $client->putObject(array(
                'ACL' => 'public-read',
                'Bucket' => BUCKET,
                'Key' => KEYS3 . $nameFile,
                'SourceFile' => $sourceFileName,
                'ContentType' => $contentType
            ));
            return true;
        } catch (S3Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    private function S3Delete($client, $nameFile){
        // Delete a publicly file.
        try {
            $client->deleteObject([
                'Bucket' => BUCKET, // REQUIRED
                'Key' => KEYS3.$nameFile, // REQUIRED
                'RequestPayer' => 'requester'
            ]);
            return true;
        } catch (S3Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    private function createOrUpdateGalleryQuery($img, $name, $mimeType){
        $em = $this->getDoctrine()->getManager();
        $ObservationGallery = $em->getRepository('UNOEvaluacionesBundle:ObservationGallery')->findOneBy(array('type' => $this->_type, 'observationId' => $this->_observationId));

        $client = S3Client::factory(
            array('credentials' => array(
                'key'    => "AKIAIHSQ7XQE4TQMSQPA",
                'secret' => "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"
            ),
                'version' => 'latest',
                'region'  => 'us-east-1'
            )
        );

        $r = false;

        if($ObservationGallery){
            //update
            if($this->S3Delete($client, $ObservationGallery->getObservationGalleryId())){
                if($this->S3Put($client, $img, $name, $mimeType)) {
                    $ObservationGallery->setObservationGalleryId($name);
                    $ObservationGallery->setDateUpload(new \DateTime());
                    $em->flush();
                    $r = true;
                }
            }

        }else{
            //create
            if($this->S3Put($client, $img, $name, $mimeType)){
                $em = $this->getDoctrine()->getManager();
                try{
                    $ObservationGallery = new ObservationGallery();
                    $ObservationGallery->setObservationGalleryId($name);
                    $ObservationGallery->setDir(DIRS3);
                    $ObservationGallery->setType($this->_type);
                    $ObservationGallery->setDateUpload(new \DateTime());
                    $ObservationGallery->setObservationId($this->_observationId);

                    $em->persist($ObservationGallery);
                    $em->flush();
                    $r = true;
                } catch(\Exception $e){
                    print_r($e->getMessage());
                    $r = false;
                }
            }
        }
        return $r;
    }

    /**
     * @Route("/observation/deleteImg")
     * @Method({"POST"})
     */
    public function deleteImgAction(Request $request){

        $type = $request->request->get('type');
        $observationId = $request->request->get('observationId');

        $client = S3Client::factory(
            array('credentials' => array(
                'key'    => "AKIAIHSQ7XQE4TQMSQPA",
                'secret' => "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"
            ),
                'version' => 'latest',
                'region'  => 'us-east-1'
            )
        );

        if($observationId){
            $em = $this->getDoctrine()->getManager();
            $ObservationGallery = $em->getRepository('UNOEvaluacionesBundle:ObservationGallery')->findOneBy(array('type' => $type, 'observationId' => $observationId));

            if($ObservationGallery){
                if($this->S3Delete($client, $ObservationGallery->getObservationGalleryId())) {
                    $em->remove($ObservationGallery);
                    $em->flush();
                    return new JsonResponse(array('status' => Utils::http_response_code(200)), 200);
                }
            }else{
                return new JsonResponse(array('status' => Utils::http_response_code(404)),404);
            }
        }else{
            return new JsonResponse(array('status' => Utils::http_response_code(409)),409);
        }
    }

    /**
     * @Route("/observation/gallery/{observationId}")
     * @Method({"GET"})
     */
    public function observationGalleryAction($observationId){

        $result = $this->getGalleryQuery($observationId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getGalleryQuery($observationId){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("concat(OG.dir,OG.observationGalleryId) as ruta, OG.type, OG.dateUpload")
            ->from('UNOEvaluacionesBundle:ObservationGallery','OG')
            ->where('OG.observationId = :observationId')
            ->setParameter('observationId', $observationId)
            ->orderBy('OG.type', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/observation/disposition/{observationId}")
     * @Method({"GET"})
     */
    public function observationDispositionAction($observationId){

        $result = $this->getDispositionQuery($observationId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getDispositionQuery($observationId){
        $em = $this->getDoctrine()->getManager();
        $ObservationDisposition = $em->getRepository('UNOEvaluacionesBundle:ObservationDisposition')->findOneBy(array('observationId'=> $observationId));

        if($ObservationDisposition) {
            return array('disposition' => $ObservationDisposition->getDisposition());
        }else{
            return array('status' => 'error', 'message' => 'empty Disposition');
        }
    }

    /**
     * @Route("/observation/saveDisposition")
     * @Method({"POST"})
     */
    public function saveDispositionAction(Request $request){
        $disposition = $request->request->get('disposition');
        $observationId = $request->request->get('observationId');

        if($disposition){
            $result = $this->createOrUpdateDispositionQuery($disposition, $observationId);
        }else{
            $result = array('status' => 'error', 'message' => 'missing parameters');
        }
        

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return mixed
     */
    private function createOrUpdateDispositionQuery($disposition, $observationId){
        $em = $this->getDoctrine()->getManager();
        $ObservationDisposition = $em->getRepository('UNOEvaluacionesBundle:ObservationDisposition')->findOneBy(array('observationId'=> $observationId));

        if($ObservationDisposition) {
            $ObservationDisposition->setDisposition($disposition);
            $ObservationDisposition->setDateDisposition(new \DateTime());
            $em->flush();
            return true;
        }else{
            //create
            $em = $this->getDoctrine()->getManager();
            try{
                $ObservationDisposition = new ObservationDisposition();
                $ObservationDisposition->setObservationId($observationId);
                $ObservationDisposition->setDisposition($disposition);
                $ObservationDisposition->setDateDisposition(new \DateTime());

                $em->persist($ObservationDisposition);
                $em->flush();
                return true;
            } catch(\Exception $e){
                print_r($e->getMessage());
                return false;
            }
            
        }
    }

    /**
     * @Route("/observation/activity/{observationId}")
     * @Method({"GET"})
     */
    public function observationActivityAction($observationId){

        $result = $this->getActivityQuery($observationId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getActivityQuery($observationId){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("OA.observationActivityId, OA.activity, OA.startActivity, OA.endActivity")
            ->from('UNOEvaluacionesBundle:ObservationActivity','OA')
            ->where('OA.observationId = :observationId')
            ->setParameter('observationId', $observationId)
            ->orderBy("OA.observationActivityId", 'DESC')
            ->getQuery()
            ->getResult();

    }

    /**
     * @Route("/observation/finishedActivity")
     * @Method({"POST"})
     */
    public function finishedActivityAction(Request $request){

        $observationActivityId= $request->request->get('observationActivityId');

        $em = $this->getDoctrine()->getManager();
        $ObservationActivity = $em->getRepository('UNOEvaluacionesBundle:ObservationActivity')->findOneBy(array('observationActivityId'=> $observationActivityId));

        if($ObservationActivity) {
            $ObservationActivity->setEndActivity(new \DateTime());
            $em->flush();
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }

    }

    /**
     * @Route("/observation/deleteActivity")
     * @Method({"POST"})
     */
    public function deleteActivityAction(Request $request){

        $observationActivityId= $request->request->get('observationActivityId');

        $em = $this->getDoctrine()->getManager();
        $ObservationActivity = $em->getRepository('UNOEvaluacionesBundle:ObservationActivity')->findOneBy(array('observationActivityId'=> $observationActivityId));

        if($ObservationActivity) {
            $em->remove($ObservationActivity);
            $em->flush();
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }

    }

    /**
     * @Route("/observation/startActivity")
     * @Method({"GET"})
     */
    public function startActivityIdAction(){
        return new JsonResponse(array('startActivity' => new \DateTime()), 200);
    }

    /**
     * @Route("/observation/saveActivity")
     * @Method({"POST"})
     */
    public function saveActivityAction(Request $request){
        $activity = $request->request->get('activity');
        $startActivity = $request->request->get('startActivity');
        $observationId = $request->request->get('observationId');

        //create
        $em = $this->getDoctrine()->getManager();
        try{
            $ObservationActivity = new ObservationActivity();
            $ObservationActivity->setActivity($activity);
            $ObservationActivity->setStartActivity(new \DateTime($startActivity));
            //$ObservationActivity->setEndActivity(new \DateTime());
            $ObservationActivity->setObservationId($observationId);

            $em->persist($ObservationActivity);
            $em->flush();
            return new JsonResponse(
                array(
                    'observationActivityId' => $ObservationActivity->getObservationActivityId()
                ), 200);
        } catch(\Exception $e){
            return new JsonResponse(array('error' => $e->getMessage()), 400);
        }
    }

    /**
     * @Route("/observation/editActivity")
     * @Method({"POST"})
     */
    public function editActivityAction(Request $request){
        $activity = $request->request->get('activity');
        $observationActivityId = $request->request->get('observationActivityId');

        //update
        $em = $this->getDoctrine()->getManager();
        $ObservationDisposition = $em->getRepository('UNOEvaluacionesBundle:ObservationActivity')->findOneBy(array('observationActivityId'=> $observationActivityId));

        if($ObservationDisposition) {
            $ObservationDisposition->setActivity($activity);
            $em->flush();
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }
    }


    /**
     * @Route("/observation/sendMail/{observationId}")
     * @Method({"GET"})
     */
    public function sendMailAction($observationId){

        $result = $this->getMail($observationId);
        
        foreach ($result as $value) {
            $mesg = $this->buildMessage(
                'Observacion en el Aula',
                'UNOEvaluacionesBundle:Notifications:finishObservation.html.twig',
                array(
                    'Director' => $value['Direcctor'],
                    'Docente' => $value['Docente'],
                    'school' => $value['school'],
                    'nameGrade' => $value['nameGrade'],
                    'groupId' => $value['groupId'],
                    'nameProgram' => $value['nameProgram'],
                    'Coach' => $value['Coach'],
                    'host' => 'pre-ruta.unoi.com',
                    'scheme' => 'https'
                ),
                $value['emailDirecctor'],
                'Observacion-Finalizada');
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($mesg);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getMail($observationId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("CONCAT(P.name, ' ', P.surname) AS Direcctor,
                            P.email AS emailDirecctor,
                            O.observationId,
                            O.personId AS DocenteId,
                            CONCAT(P2.name, ' ', P2.surname) AS Docente,
                            P2.email AS emailDocente,
                            S.school,
                            PA.schoolLevelId,
                            Cg.nameGrade,
                            PA.groupId,
                            Cp.nameProgram,
                            O.coachId,
                            CONCAT(P3.name, ' ', P3.surname) AS Coach")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->innerJoin('UNOEvaluacionesBundle:Personassigned','PA','WITH','O.personId = PA.personId AND O.gradeId = PA.gradeId AND O.groupId = PA.groupId')
            ->innerJoin('UNOEvaluacionesBundle:Cgrade','Cg','WITH','PA.gradeId = Cg.gradeId')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','PA.programId = Cp.programId')
            ->innerJoin('UNOEvaluacionesBundle:Person','P2','WITH','PA.personId = P2.personid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P3','WITH','O.coachId = P3.personid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS','WITH','O.schoolId = PS.schoolid AND PA.schoolLevelId = PS.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S', 'WITH', 'O.schoolId = S.schoolid')
            ->where('O.observationId = :observationId')
            ->andWhere('O.finish IS NOT NULL')
            ->andWhere('PS.profileid = 19')
            ->setParameter('observationId', $observationId)
            ->getQuery()
            ->getResult();
    }

    /**
     *
     * Construcción de mensaje para correo
     *
     * @param $title "Titulo del mensaje"
     * @param $view "Enlace de la vista que ocupara el mensaje"
     * @param $params "Parámetros de la vista"
     * @param $recipient "Dirección de correo electrónico de quien recibe el mensaje"
     * @return int "Cantidad de correos enviados"
     * @throws \Exception
     * @throws \Twig_Error
     */

    private function buildMessage($title, $view, $params, $recipient,$tag){

        setlocale(6,"es_MX.utf8");
        $sendDate = strftime("%d %B %Y");

        $message = \Swift_Message::newInstance()
            ->setSubject($title.' '.$sendDate)
            ->setFrom(array('noreplymx@unoi.com' => 'Diagnóstico Institucional UNOi'))
            ->setTo($recipient)
            ->setBody(
                $this->renderView($view, $params),
                'text/html'
            );

        $message->getHeaders()->addTextHeader('X-MC-Tags',$tag);

        return $this->get('mailer')->send($message);
    }

    /**
     * @Route("/observation/saveAspect")
     * @Method({"POST"})
     */
    public function saveAspectAction(Request $request){

        $observationId= $request->request->get('observationId');
        $type= 'set'.ucfirst($request->request->get('type'));
        $aspect= $request->request->get('aspect');

        $em = $this->getDoctrine()->getManager();
        $ObservationAspects = $em->getRepository('UNOEvaluacionesBundle:ObservationAspects')->findOneBy(array('observationId'=> $observationId));

        if($ObservationAspects) {
            $ObservationAspects->$type($aspect);
            $em->flush();
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }

    }
}
