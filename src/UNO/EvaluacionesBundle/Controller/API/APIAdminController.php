<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $em = $this->getDoctrine()->getManager();
        $PersonId = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('personid'=> $personId));

        if($PersonId) {
            $em->remove($PersonId);
            $em->flush();
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
       
        $em = $this->getDoctrine()->getManager();
        $school = $em->getRepository('UNOEvaluacionesBundle:School')->findOneBy(array('schoolid'=> $schoolId));

        if($school) {
            $em->remove($school);
            $em->flush();
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }
    }

    /**
     * @Route("/resetPerson")
     * @Method({"POST"})
     */
    public function resetPersonAction(Request $request){

        $personId= $request->request->get('personId');

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
        $surveyId= $request->request->get('surveyId');


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

        $schoolId= $request->request->get('schoolId');

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

        $schoolId= $request->request->get('schoolId');
        $surveyId= $request->request->get('surveyId');


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
}