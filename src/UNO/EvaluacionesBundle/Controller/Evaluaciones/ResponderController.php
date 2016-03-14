<?php
/**
 * Created by PhpStorm.
 * User: cgutierrezy
 * Date: 18/09/15
 * Time: 12:33
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UNO\EvaluacionesBundle\Entity\Answer;
use UNO\EvaluacionesBundle\Entity\Log;

class ResponderController extends Controller
{

    public function indexAction(Request $request, $id){
        $surveyID = $id;
        $session = $request->getSession();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'responder',
                'with' => $id
            ));

            //return $this->redirectToRoute('login');
        }

        if(!Utils::isSurveyAuthorized($session,$surveyID)){

            throw new AccessDeniedHttpException('No estás autorizado para ver este contenido');
        }

        $personId = $session->get('personIdS');
        $questions = $this->getQuestions($surveyID,$personId);
        $_survey = $this->getCurrentSurvey($surveyID);
        $surveyJson = $this->creaJson($_survey, $questions);

        return $this->render('@UNOEvaluaciones/Evaluaciones/responder.html.twig', array(
            'surveyData' => json_decode($surveyJson, true)
        ));
    }

    private function getQuestions($surveyId, $personId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $_survey = $qb->select("qxs.order, q.questionid, q.question, oxq.optionxquestionId, o.option, COALESCE(a.answer,' ') as ans")
            ->from('UNOEvaluacionesBundle:Question','q')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.questionQuestionid = q.questionid AND qxs.surveySurveyid = :surveyId')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq', 'WITH', 'qxs.questionxsurveyId = oxq.questionxsurvey')
            ->leftJoin('UNOEvaluacionesBundle:Option','o', 'WITH', 'oxq.optionOptionid = o.optionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','a','WITH','oxq.optionxquestionId = a.optionxquestion AND a.personPersonid = :personId')
            ->groupBy('qxs.order, q.questionid, q.question, oxq.optionxquestionId')
            ->orderBy('qxs.order')
            ->setParameter('surveyId',$surveyId)
            ->setParameter('personId',$personId)
            ->getQuery()
            ->getResult();

        $questions = array();
        foreach($_survey as $q) {

            if(!array_key_exists($q['questionid'],$questions)){

                $questions[$q['questionid']] = array(
                    'questionid' => $q['questionid'],
                    'order' => $q['order'],
                    'question' => $q['question'],
                    'options' => array(),
                    'answer' => ''
                );
            }

            array_push($questions[$q['questionid']]['options'],array(
                'option' => $q['option'],
                'optionxquestionId' => $q['optionxquestionId']
            ));

            if($q['ans'] != ' ') {

                $questions[$q['questionid']]['answer'] = $q['ans'];
            }
        }
        return $questions;
    }

    private function creaJson($_survey,$questions) {

        $qsToAnswer = array();
        foreach($questions as $q){

            if($q['answer'] == '') {
                array_push($qsToAnswer,$q);
            }
        }

        $json_pack = array(
            'surveyid' => $_survey->getSurveyid(),
            'title' => $_survey->getTitle(),
            'description' => $_survey->getDescription(),
            'questions' => $qsToAnswer
        );

        return json_encode($json_pack);
    }

    private function getOptionXQuestion($id){
        return $this->getDoctrine()->getRepository('UNOEvaluacionesBundle:Optionxquestion')->find($id);
    }

    private function getCurrentSurvey($id){
        $survey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Survey')
            ->find($id);

        return $survey;
    }

    private function getPerson($id){
        return $this->getDoctrine()->getRepository('UNOEvaluacionesBundle:Person')->find($id);
    }

    public function guardarAction(Request $request){
        $session = $request->getSession();
        $personId = $session->get('personIdS');
        $content = $this->get("request")->getContent();
        $survey = "";
        if (!empty($content)) {
            $params = json_decode($content, true);
            $i = 0;
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{
                $personIdObj = $this->getPerson($personId);
                foreach ($params as $pms) {
                    $survey = ($survey == "" ? $this->getCurrentSurvey($pms['surveyid']) : $survey);
                    $ans = new Answer();
                    $ans->setAnswer($pms['Answer']);
                    $ans->setComment($pms['Comment']);
                    $ans->setOptionxquestion($this->getOptionXQuestion($pms['optionxquestionId']));
                    $ans->setPersonPersonid($personIdObj);
                    $em->persist($ans);
                    $em->flush();
                    $i++;
                }
                $em->getConnection()->commit();
                $response = json_encode(array('message' => 'Se guardaron ' .  $i . ' preguntas'));
                $this->createLog('004',$survey, $personId);
                return new Response($response, 200, array(
                    'Content-Type' => 'application/json'
                ));
            } catch (\Exception $ex){

                $em->getConnection()->rollback();
                $response = json_encode(array('message' => $ex->getMessage()));
                return new Response($response, 500, array(
                    'Content-Type' => 'application/json'
                ));
            }
        }else{
            $response = json_encode(array('message' => 'Error Empty'));
            return new Response($response, 500, array(
                'Content-Type' => 'application/json'
            ));
        }
    }

    private function createLog($status,$survey, $personId) {
        $em = $this->getDoctrine()->getManager();
        $newAction = $this->getDoctrine()->getRepository('UNOEvaluacionesBundle:Action')->findOneBy(array("actioncode"=>$status));
        $newLog = new Log();
        $newLog->setPersonPersonid( $personId );
        $newLog->setSurveySurveyid( $survey->getSurveyid() );
        $newLog->setDate( new \DateTime("now") );
        $newLog->setActionaction( $newAction );
        $em->persist( $newLog );
        $em->flush();
    }
}