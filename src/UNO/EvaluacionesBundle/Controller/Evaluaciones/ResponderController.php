<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:33
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Option;
use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Question;
use Proxies\__CG__\UNO\EvaluacionesBundle\Entity\Questionxsurvey;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Responder controller.
 *
 * @Route("/responder")
 */

class ResponderController extends Controller
{

    public function indexAction(){

        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        //---------- Getting the SURVEY
        $surveyID = 1;

        /*$surveyQuery =
            'SELECT * FROM unoevaluaciones.Survey
             WHERE Survey.surveyId =:surveyID';

        $statement = $connection->prepare($surveyQuery);
        $statement->bindValue('surveyID',$surveyID);

        $statement->execute();
        $survey = $statement->fetchAll();*/

        $survey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Survey')
            ->find($surveyID);

        //--------- Getting the QUESTIONS from QuestionsXSurvey




        //--- first step: get all Question IDs
        $questionsXSurvey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Questionxsurvey')
            ->findBy(
                array('surveySurveyid' => $surveyID)
            );


        // ---- step two: get each question us¡ng teh IDs and push them into an array
        $questions = array();
        $options = array();

        foreach($questionsXSurvey as $questionItem ){
            $singleQuestion = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Question')
                ->find( $questionItem->getquestionQuestionid()->getquestionid()  );

            $questionsXSurveyID = $questionItem->getquestionxsurveyId();
            $questionID = $questionItem->getquestionQuestionid()->getquestionid();
            $questionText = $questionItem->getquestionQuestionid()->getquestion();

            $thisOptions = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Optionxquestion')
                ->findBy(
                    array('questionxsurvey'=>$questionsXSurveyID)
                );

            foreach( $thisOptions as $eachOption ){
                $eachOptionQuery =
                    'SELECT * FROM unoevaluaciones.Option
                     WHERE Option.optionId =:optionID';

                $statement = $connection->prepare($eachOptionQuery);
                $statement->bindValue('optionID', $eachOption->getOptionOptionid()->getOptionid() );

                $statement->execute();
                $allOptions = $statement->fetchAll();

                array_push($options, $allOptions);
            }
            var_dump($options);
            echo "<br>";
            echo "<hr>";
            var_dump( count($thisOptions). "--".$questionsXSurveyID."-".$questionID."-".$questionText);
            echo "<br>";
            //array_push($questions, $singleQuestion);

        }

        exit;

        $someDataA = array(
            'cosa1'=>'A',
            'cosa2'=>'B',
            'cosa3'=>'C',
        );

        $someDataB = array(
            'cosa1'=>'D',
            'cosa2'=>'E',
            'cosa3'=>'F',
        );

        $someDataC = array(
            'datos1'=>$someDataA,
            'datos2'=>$someDataB,
        );

        $someData = array( $someDataA, $someDataB );

        //rendering teh VIEW
        return $this->render('@UNOEvaluaciones/Evaluaciones/responder.html.twig', array(

            'survey' => $survey,
            'questions' => $questions,
            'somedata' => $someData,

        ));
    }
}