<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:18
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ListarController extends Controller
{
    public function indexAction(Request $request) {
        $surveyList = array();
        //I'll get these information (personiD, user, name) from session variable
        $personID = '3066990';

        //Gettin' profiles
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query = 'SELECT Profile.profileId FROM unoevaluaciones.Profile INNER JOIN PersonSchool ON PersonSchool.profileId = Profile.profileId INNER JOIN Person ON Person.personId = PersonSchool.personId WHERE Person.personId =:personID';

        $statement = $connection->prepare($query);
        $statement->bindValue('personID',$personID);
        $statement->execute();
        $resultset = $statement->fetchAll();
        $profiles = '';

        foreach($resultset as $r) {

            $profiles .= $r['profileId'].', ';
        }
        $profiles = rtrim($profiles,', ');

        // Gettin' all active surveys according to user profile(s)
        // Tried with dql but returns nothing when using IN clause -_-

        $query = 'SELECT surveyId, title, closingDate FROM unoevaluaciones.Survey INNER JOIN SurveyXProfile ON SurveyXProfile.Survey_surveyId = Survey.surveyId WHERE SurveyXProfile.Profile_profileId IN ('.$profiles.') AND active = 1 GROUP BY surveyId, title, closingDate';

        $statement = $connection->prepare($query);
        $statement->execute();
        $resultset = $statement->fetchAll();

        // Check log for Survey status query
        $query = 'SELECT Action_idAction FROM Log WHERE Person_personId =:personId AND Survey_surveyId =:surveyId AND Log.Action_idAction IN (4,5) ORDER BY ABS(now() - Log.date) ASC';

        $countToBeAnswered = 0;
        foreach ($resultset as $survey){

            $statement = $connection->prepare($query);
            $statement->bindValue('personId',$personID);
            $statement->bindValue('surveyId',$survey['surveyId']);
            $statement->execute();
            $status = $statement->fetch();

            if($status['Action_idAction'] == 5 || !$status) {

                $countToBeAnswered++;
            }

            array_push($surveyList, array(

                'id' => $survey['surveyId'],
                'title' => $survey['title'],
                'closingDate' => date_format(date_create($survey['closingDate']), 'j/M/Y \@ g:i a'),
                'status' => (!is_null($status['Action_idAction']) ? $status['Action_idAction']: '0'),
            ));
        }

        $statistics = $this->fetchStats(count($resultset),$countToBeAnswered);

        return $this->render('@UNOEvaluaciones/Evaluaciones/listar.html.twig', array(

            'surveyList' => $surveyList,
            'stats' => $statistics,
        ));
    }

    private function fetchStats($countSurveys, $countToBeAnswered){

        $answeredCount = $countSurveys - $countToBeAnswered;
        $compliancePercentage = ($countSurveys > 0 ? (($answeredCount * 100)/$countSurveys): 0);

        return array(
            'answered' => $answeredCount,
            'toBeAnswered' => $countToBeAnswered,
            'compliance' => $compliancePercentage,
        );
    }
}