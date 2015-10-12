<?php
/**
 * Created by PhpStorm.
 * User: cgutierrezy
 * Date: 18/09/15
 * Time: 12:33
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Action;
use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Answer;
use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Option;
use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Question;
use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Questionxsurvey;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\DateTime;
use UNO\EvaluacionesBundle\Entity\Log;



/**
 * Responder controller.
 *
 * @Route("/responder")
 */

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
            }

        }

        //---------- Getting the current SURVEY
        $survey = $this->getCurrentSurvey($surveyID);

         if(count($this->findLog($personId, $surveyID, '004' )) > 0 ){
             return $this->render('@UNOEvaluaciones/Evaluaciones/responderError.html.twig',array(
                 'title'=>'Error',
                 'message'=>'Est encuesta ya ha sido contestada previamente',
             ));
         }


        /*
        * when the user complete a survey then a POST data is sent
        * so if that data exists the answers and a log are inserted
        * and redirect to the List of surveys
         *
         * If the data doesn't exists it means the user gonna answer the survey
        */
        if( isset($_POST['answers']) ){
            $this->insertAnswers( $request,$personId);

            //Create the log for "COMPLETE" status
            $this->createLog('004',$survey, $personId);
            return $this->redirectToRoute('listar');

        }else {
            //Create the log for "INCOMPLETE" status
            $this->createLog('005', $survey, $personId);

            //--- first step: get all Question IDs ordered by ORDER
            $allQuestions = $this->getAllQuestions($survey);

            //-- step two: we create the Itmes for the view
            $items = $this->createItems($allQuestions);

            //rendering teh VIEW
            return $this->render('@UNOEvaluaciones/Evaluaciones/responder.html.twig', array(
                'items' => $items,
                'surveyName' => $survey->getTitle(),
                'surveyDesc' => $survey->getDescription(),
            ));
        }
    }

    public function findLog($personId, $surveyId, $action){

        $action = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Action')
            ->findOneBy(
                array('actioncode'=>$action)
            );

        $criteria = array_filter(array(
            'actionaction' => $action,
            'surveySurveyid' => $surveyId,
            'personPersonid' => $personId
        ));


        $log = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Log')
            ->findBy( $criteria );

        return $log;
    }

    /*
     * This function inserts the answers using the data from the POST
     *
     */
    public function insertAnswers(Request $request,$personId ){
        $em = $this->getDoctrine()->getManager();

        $answersItems = json_decode( $request->request->get('answers'),true );

        foreach( $answersItems as $answerItem ){

            $newAnswer = new Answer();
            $newAnswer->setAnswer( $answerItem['answer'] );
            $newAnswer->setComment( $answerItem['comment'] );

            $newPerson = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Person')
                ->find( $personId );

            $newAnswer->setPersonPersonid( $newPerson );

            $newOptionXQuestion = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Optionxquestion')
                ->find( $answerItem['OptionXQuestion_id'] );

            $newAnswer->setOptionxquestion( $newOptionXQuestion );

            $em->persist( $newAnswer );
            $em->flush();

        }
    }

    /*
     *This function creates a Log using the SurveyID (only the ID),
     * PersonID (only ID), date, and teh action (COMPLETE,INCOMPLETE, etc)
     * */
    private function createLog($status,$survey, $personId){
        $em = $this->getDoctrine()->getManager();

        $newLog = new Log();

        $newPerson = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Person')
            ->find( $personId );
        $newLog->setPersonPersonid( $newPerson->getPersonid() );

        $newLog->setSurveySurveyid( $survey->getSurveyid() );

        $newLog->setDate( new \DateTime("now") );

        $newAction = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Action')
            ->findOneBy(
                array("actioncode"=>$status)
            );

        $newLog->setActionaction( $newAction );

        $em->persist( $newLog );
        $em->flush();
    }

    /*
     * This function gets the Survey from DB using the ID
     * returns a Survey
     * */
    private function getCurrentSurvey($id){
        $survey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Survey')
            ->find($id);

        return $survey;
    }

    /*
     * This function gets all questions
     * receives a Survey
     * returns array[Question]
     * */
    private function getAllQuestions($survey){
        $questionsXSurvey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Questionxsurvey')
            ->findBy(
                array('surveySurveyid' => $survey->getSurveyid()),
                array('order'=>'ASC')
            );

        return $questionsXSurvey;
    }

    /*
     * This function create the Items array for the view
     * receives an array[Question]
     * returns an array
     * */
    private function createItems($questionsList){
        $itemsList = array();

        foreach($questionsList as $questionItem ){

            array_push($itemsList, $this->createSingleItem($questionItem) );

        }
        return $itemsList;
    }

    /*
     *This function creates a single Item for the final itemsList
     * This function receives a Question
     * returns an array with the properties we need for the view
     * */
    private function createSingleItem($questionItem){
        $questionsXSurveyID = $questionItem->getquestionxsurveyId();

        $singleQuestion = $this->getSingleQuestion($questionItem->getquestionQuestionid()->getquestionid());

        $thisOptions = $this->getOptions($questionsXSurveyID);

        $thisItem = [
            'questionsxsurveyid'=>$questionsXSurveyID,
            'questionid'=>$singleQuestion->getquestionid(),
            'question'=>$singleQuestion->getquestion(),
            'required'=>$singleQuestion->getRequired(),
            'order'=>$questionItem->getOrder(),
            'options'=> $this->createOptions($thisOptions),

        ];
        return $thisItem;
    }


    /*
     *This function returns a single question from DB
     * returns a Question
     * */
    private function getSingleQuestion($id){
        $singleQuestion = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Question')
            ->find( $id  );

        return $singleQuestion;
    }

    /*
     *This function gets all options
     * receives an ID from QuestionXSurvey table
     * returns an array[Optionxquestion]
     * */
    private function getOptions($questionxsurveyID){
        $options = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Optionxquestion')
            ->findBy(
                array('questionxsurvey'=>$questionxsurveyID),
                array('order'=>'ASC')
            );
        return $options;

    }

    /*
     * This function finds the properties we will need for each Option
     * receives an array[Option]
     * returns an array with the properties
     * */
    private function createOptions($optionsList){
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $optionsArray = array();

        foreach( $optionsList as $eachOption ){
            $eachOptionQuery =
                'SELECT * FROM unoevaluaciones.Option
                     WHERE Option.optionId =:optionID';

            $statement = $connection->prepare($eachOptionQuery);
            $statement->bindValue('optionID', $eachOption->getOptionOptionid()->getOptionid() );

            $statement->execute();
            $optionFound = $statement->fetchAll();

            array_push($optionsArray,array(
                'optionxquestionid'=>$eachOption->getOptionxquestionId(),
                'optionText'=>$optionFound[0]['option'],

            ));
        }
        return $optionsArray;
    }


}