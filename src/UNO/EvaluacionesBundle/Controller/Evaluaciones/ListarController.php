<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:18
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListarController extends Controller
{
    public function indexAction(){

        //I'll get these information (personiD, user, name) from session variable
        $personID = '3066990';
        $list = array();

        //Gettin' profiles

        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query =
            'SELECT Profile.profileId FROM unoevaluaciones.Profile
            INNER JOIN PersonSchool
            ON PersonSchool.profileId = Profile.profileId
            INNER JOIN Person
            ON Person.personId = PersonSchool.personId
            WHERE Person.personId =:personID';

        $statement = $connection->prepare($query);
        $statement->bindValue('personID',$personID);
        $statement->execute();
        $resultset = $statement->fetchAll();
        $profiles = '';

        foreach($resultset as $r){

            //array_push($profiles, $r['profileId']);
            $profiles .= $r['profileId'].', ';
        }

        $profiles = rtrim($profiles,', ');

        // Gettin' all surveys according to user profile(s)
        // Tried with Doctrine but no success

        $query = 'SELECT surveyId, title, closingDate FROM unoevaluaciones.Survey
        INNER JOIN SurveyXProfile ON SurveyXProfile.Survey_surveyId = Survey.surveyId
        WHERE SurveyXProfile.Profile_profileId IN ('.$profiles.') GROUP BY surveyId, title, closingDate';

        $statement = $connection->prepare($query);
        $statement->execute();
        $resultset = $statement->fetchAll();

        $query = 'SELECT Description FROM Action
          INNER JOIN Log ON
          Log.Action_idAction = Action.idAction
          WHERE Person_personId =:personId
          AND Survey_surveyId =:surveyId
          AND Log.Action_idAction IN (4,5)
          ORDER BY ABS(now() - Log.date) ASC';

        foreach ($resultset as $survey){

            $statement = $connection->prepare($query);
            $statement->bindValue('personId',$personID);
            $statement->bindValue('surveyId',$survey['surveyId']);
            $statement->execute();
            $status = $statement->fetch();

            
        }

        return $this->render('@UNOEvaluaciones/Evaluaciones/listar.html.twig', array(

            'survey_list' => $resultset,

        ));
    }
}