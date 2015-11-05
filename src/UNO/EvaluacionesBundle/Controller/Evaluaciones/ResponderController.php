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
use UNO\EvaluacionesBundle\Entity\Answer;
use UNO\EvaluacionesBundle\Entity\Log;

class ResponderController extends Controller
{

    public function indexAction(Request $request, $id){
        $surveyID = $id;
        $session = $request->getSession();
        /*
         * This section checks if the user is already logged in,
         * in that case $personId gets the personIdS value from the session variable
         * otherwise is redirected to the Login
         * */
        if ( !$session->has('logged_in') ) {
            return $this->redirectToRoute('login');
        }else{
            $personId = $session->get('personIdS');
            if(!Utils::isSurveyAuthorized($session,$surveyID)){
                return $this->render('@UNOEvaluaciones/Evaluaciones/responderError.html.twig',array(
                    'title'=>'Error',
                    'message'=>'Lo sentimos, el contenido que buscas es erróneo',
                ));
            }else{
                $_survey = $this->getQuestions($surveyID);
                $surveyJson = $this->creaJson($_survey);
                return $this->render('@UNOEvaluaciones/Evaluaciones/responder.html.twig', array(
                    'surveyData' => json_decode($surveyJson, true)
                ));
            }
        }
    }

    private function getQuestions($surveyId){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_survey = $qb->select("S.surveyid, S.title, S.description, Q.questionid, QS.order, Q.question, Sub.subcategoryid, Sub.subcategory, OQ.optionxquestionId, O.optionid, O.option")
            ->from('UNOEvaluacionesBundle:Survey','S')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'S.surveyid = QS.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'QS.questionxsurveyId = OQ.questionxsurvey')
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.surveyid = :surveyId')
            ->setParameter('surveyId', $surveyId)
            ->orderBy('Q.questionid, QS.order, O.optionid')
            ->getQuery()
            ->getResult();

        return $_survey;
    }

    private function creaJson($_survey){
        $surveyJson = array(
            'surveyid' => $_survey[0]['surveyid'],
            'title' => $_survey[0]['title'],
            'description' => $_survey[0]['description']
        );

        $question = array();

        foreach(array_unique(array_column($_survey,'questionid','order')) as $valQue){
            $i = 0;
            foreach($_survey as $valSur){
                if($valQue == $valSur['questionid']){
                    $options['questionid'] = $valSur['questionid'];
                    $options['order'] = $valSur['order'];
                    $options['question'] = $valSur['question'];
                    $options['subcategoryid'] = $valSur['subcategoryid'];
                    $options['subcategory'] = $valSur['subcategory'];

                    $options['options'][$i] = array(
                        "optionid" => $valSur['optionid'],
                        "option" => $valSur['option'],
                        "optionxquestionId" => $valSur['optionxquestionId']
                    );
                    $i++;
                }
            }
            array_push($question,$options);
        }
        $surveyJson['questions'] = $question ;
        //print_r($surveyJson);
        return json_encode($surveyJson);
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

    private function createLog($status,$survey, $personId){
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