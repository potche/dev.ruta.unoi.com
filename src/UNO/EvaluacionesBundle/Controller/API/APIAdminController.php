<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\LogDeleteReset;
use UNO\EvaluacionesBundle\Entity\Userhttpsession;
use UNO\EvaluacionesBundle\Controller\API\APIUtils;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 29/08/16
 * Time: 3:05 PM
 */

/**
 * @Route("/api/v0/admin")
 *
 */
class APIAdminController extends Controller{

    /**
     * @Route("/deletePerson")
     * @Method({"POST"})
     */
    public function deletePersonAction(Request $request){

        $personId= $request->request->get('personId');
        $user= $request->request->get('user');
        $name= $request->request->get('name');
        $schoolId= $request->request->get('schoolId');
        $school= $request->request->get('school');
        $executioner= $request->request->get('executioner');

        $em = $this->getDoctrine()->getManager();
        $PersonId = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid'=> $personId));

        if($PersonId) {
            $em->remove($PersonId);
            $em->flush();
            $this->log(array('action' => 'Delete', 'personId' => $personId, 'user' => $user, 'name' => $name, 'schoolId' => $schoolId, 'school' => $school, 'surveyId' => null, 'survey' => null, 'executioner' => $executioner));
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }
    }

    /**
     * @Route("/deleteSchool")
     * @Method({"POST"})
     */
    public function deleteSchoolAction(Request $request){

        $schoolId= $request->request->get('schoolId');
        $schoolName= $request->request->get('school');
        $executioner= $request->request->get('executioner');

        $result = $this->executeQuery(
            "DELETE P
            FROM School S
            inner join PersonSchool PS on S.schoolId = PS.schoolId
            inner join Person P on PS.PersonId = P.personId
            where S.schoolId = $schoolId
            "
        );

        if($result) {
            $em = $this->getDoctrine()->getManager();
            $school = $em->getRepository('UNOEvaluacionesBundle:School')->findOneBy(array('schoolid' => $schoolId));

            if ($school) {
                $em->remove($school);
                $em->flush();

                $this->log(array('action' => 'Delete', 'personId' => null, 'user' => null, 'name' => null, 'schoolId' => $schoolId, 'school' => $schoolName, 'surveyId' => null, 'survey' => null, 'executioner' => $executioner));
                return new JsonResponse(array('status' => true), 200);
            } else {
                return new JsonResponse(array('status' => false), 400);
            }
        }else {
            return new JsonResponse(array('status' => false), 400);
        }
    }

    /**
     * @Route("/resetPerson")
     * @Method({"POST"})
     */
    public function resetPersonAction(Request $request){

        $personId= $request->request->get('personId');
        $user= $request->request->get('user');
        $name= $request->request->get('name');
        $schoolId= $request->request->get('schoolId');
        $school= $request->request->get('school');
        $executioner= $request->request->get('executioner');

        $result = $this->executeQuery(
            "DELETE A
            FROM QuestionXSurvey QS
            inner join OptionXQuestion OQ on QS.QuestionxSurvey_id = OQ.QuestionXSurvey_id
            inner join Answer A on OQ.OptionXQuestion_id = A.OptionXQuestion_id
            where A.Person_personId = $personId
            "
        );

        if($result) {
            $result2 = $this->executeQuery(
                "DELETE
                FROM Log
                where Person_personId = $personId
                "
            );

            if($result2) {
                $this->log(array('action' => 'Reset Stats', 'personId' => $personId, 'user' => $user, 'name' => $name, 'schoolId' => $schoolId, 'school' => $school, 'surveyId' => null, 'survey' => null,'executioner' => $executioner));
                return new JsonResponse(array('status' => true),200);
            }else{
                return new JsonResponse(array('status' => false),400);
            }
        }

    }

    /**
     * @Route("/resetPersonSurvey")
     * @Method({"POST"})
     */
    public function resetPersonSurveyAction(Request $request){

        $personId= $request->request->get('personId');
        $user= $request->request->get('user');
        $name= $request->request->get('name');
        $schoolId= $request->request->get('schoolId');
        $school= $request->request->get('school');
        $surveyId= $request->request->get('surveyId');
        $survey= $request->request->get('survey');
        $executioner= $request->request->get('executioner');

        $result = $this->executeQuery(
            "DELETE A
            FROM QuestionXSurvey QS
            inner join OptionXQuestion OQ on QS.QuestionxSurvey_id = OQ.QuestionXSurvey_id
            inner join Answer A on OQ.OptionXQuestion_id = A.OptionXQuestion_id
            where A.Person_personId = $personId
            and QS.Survey_surveyId = $surveyId
            "
        );

        if($result) {
            $result2 = $this->executeQuery(
                "DELETE
                FROM Log
                where Person_personId = $personId
                and Survey_surveyId = $surveyId
                "
            );

            if($result2) {
                $this->log(array('action' => 'Reset Stats', 'personId' => $personId, 'user' => $user, 'name' => $name, 'schoolId' => $schoolId, 'school' => $school, 'surveyId' => $surveyId, 'survey' => $survey, 'executioner' => $executioner));
                return new JsonResponse(array('status' => true),200);
            }else{
                return new JsonResponse(array('status' => false),400);
            }
        }

    }

    /**
     * @Route("/resetSchool")
     * @Method({"POST"})
     */
    public function resetSchoolAction(Request $request){

        $schoolId = $request->request->get('schoolId');
        $school = $request->request->get('school');
        $executioner = $request->request->get('executioner');

        $result = $this->executeQuery(
            "DELETE A
            FROM QuestionXSurvey QS
            inner join OptionXQuestion OQ on QS.QuestionxSurvey_id = OQ.QuestionXSurvey_id
            inner join Answer A on OQ.OptionXQuestion_id = A.OptionXQuestion_id
            where A.Person_personId = $schoolId
            "
        );

        if($result) {
            $result2 = $this->executeQuery(
                "DELETE L
                FROM
                    PersonSchool PS
                        INNER JOIN
                    SurveyXProfile SP ON PS.profileId = SP.Profile_profileId
                        AND PS.schoolLevelId = SP.schoolLevelId
                        INNER JOIN
                    Survey S ON SP.Survey_surveyId = S.surveyId
                        INNER JOIN
                    School Sc ON Sc.schoolId = PS.schoolId
                        INNER JOIN
                    Person P ON PS.personId = P.personId
                        INNER JOIN
                    Log L ON P.personId = L.Person_personId
                        AND S.surveyId = L.Survey_surveyId
                WHERE
                    Sc.schoolId = $schoolId
                "
            );

            if($result2) {
                $this->log(array('action' => 'Reset Stats', 'personId' => null, 'user' => null, 'name' => null, 'schoolId' => $schoolId, 'school' => $school, 'surveyId' => null, 'survey' => null, 'executioner' => $executioner));
                return new JsonResponse(array('status' => true),200);
            }else{
                return new JsonResponse(array('status' => false),400);
            }
        }
    }

    /**
     * @Route("/resetSchoolSurvey")
     * @Method({"POST"})
     */
    public function resetSchoolSurveyAction(Request $request){

        $schoolId = $request->request->get('schoolId');
        $school = $request->request->get('school');
        $surveyId = $request->request->get('surveyId');
        $survey = $request->request->get('survey');
        $executioner = $request->request->get('executioner');

        $result = $this->executeQuery(
            "DELETE A
            FROM
                QuestionXSurvey QS
                    INNER JOIN
                OptionXQuestion OQ ON QS.QuestionxSurvey_id = OQ.QuestionXSurvey_id
                    INNER JOIN
                Answer A ON OQ.OptionXQuestion_id = A.OptionXQuestion_id
                    INNER JOIN
                PersonSchool PS ON A.Person_personId = PS.personId
            WHERE
                PS.schoolId = $schoolId
                    AND QS.Survey_surveyId = $surveyId
            "
        );

        if($result) {
            $result2 = $this->executeQuery(
                "DELETE L
                FROM
                    PersonSchool PS
                        INNER JOIN
                    SurveyXProfile SP ON PS.profileId = SP.Profile_profileId
                        AND PS.schoolLevelId = SP.schoolLevelId
                        INNER JOIN
                    Survey S ON SP.Survey_surveyId = S.surveyId
                        INNER JOIN
                    School Sc ON Sc.schoolId = PS.schoolId
                        INNER JOIN
                    Person P ON PS.personId = P.personId
                        INNER JOIN
                    Log L ON P.personId = L.Person_personId
                        AND S.surveyId = L.Survey_surveyId
                WHERE
                    Sc.schoolId = $schoolId
                    AND S.surveyId = $surveyId
                "
            );

            if($result2) {
                $this->log(array('action' => 'Reset Stats', 'personId' => null, 'user' => null, 'name' => null, 'schoolId' => $schoolId, 'school' => $school, 'surveyId' => $surveyId, 'survey' => $survey, 'executioner' => $executioner));
                return new JsonResponse(array('status' => true),200);
            }else{
                return new JsonResponse(array('status' => false),400);
            }
        }
    }

    private function executeQuery($query){
        $em = $this->getDoctrine()->getManager();

        $em->getConnection()->beginTransaction();


        try {
            $statement = $em->getConnection()->prepare($query);
            $statement->execute();
            $em->getConnection()->commit();
            return true;

        }catch (\PDOException $e) {
                throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete expired sessions: %s', $e->getMessage()), 0, $e);
        }
    }

    private function log($log){

        $LogDeleteReset = new LogDeleteReset();

        $LogDeleteReset->setAction($log["action"]);
        $LogDeleteReset->setPersonId($log["personId"]);
        $LogDeleteReset->setUser($log["user"]);
        $LogDeleteReset->setName($log["name"]);
        $LogDeleteReset->setSchoolId($log["schoolId"]);
        $LogDeleteReset->setSchool($log["school"]);
        $LogDeleteReset->setSurveyId($log["surveyId"]);
        $LogDeleteReset->setSurvey($log["survey"]);
        $LogDeleteReset->setDateAction(new \DateTime());
        $LogDeleteReset->setIpCliente($_SERVER['REMOTE_ADDR']);
        $LogDeleteReset->setExecutioner($log["executioner"]);

        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($LogDeleteReset);
        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
    }
}