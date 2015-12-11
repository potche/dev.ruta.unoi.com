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
 * Date: 01/12/15
 * Time: 3:05 PM
 */

/**
 * @Route("/api/v0/result")
 */
class APIResultsController extends Controller{

    /**
     * @Route("/survey/{surveyId}/person/{personId}",
     * requirements={"surveyId" = "\d+", "personId" = "\d+"},
     * defaults={"personId" = null})
     * @Method({"GET"})
     */
    public function resultSurveyPersonAction($surveyId, $personId){

        $surveyPerson = $this->getResQuery(array('personId' => $personId, 'surveyId' => $surveyId), 'P.personid = :personId AND S.surveyid = :surveyId');

        if ($surveyPerson) {
            $result = $this->getJSON($surveyPerson);
        } else {
            $result = APIUtils::getErrorResponse(404);
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/survey/{surveyId}/school/{schoolId}"),
     * requirements={"surveyId" = "\d+", "schoolId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSurveySchoolAction(Request $request, $surveyId, $schoolId){

        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $surveySchool = $this->getResQuery(array('schoolId' => $schoolId, 'surveyId' => $surveyId), 'PS.schoolid = :schoolId AND S.surveyid = :surveyId');

            if ($surveySchool) {
                $result = $this->getJSON($surveySchool);
            } else {
                $result = APIUtils::getErrorResponse(404);
            }
        }else{
            $result = APIUtils::getErrorResponse(403);
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/school/{schoolId}/profile/{profileId}",
     * requirements={"schoolId" = "\d+", "profileId" = "\d+"})
     * @Method({"GET"})
     */
    public function resultSchoolProfileAction(Request $request, $schoolId, $profileId){

        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $schoolProfile = $this->getResQuery(array('schoolId' => $schoolId, 'profileId' => $profileId), 'PS.schoolid = :schoolId AND PS.profileid = :profileId');

            if ($schoolProfile) {
                $result = $this->getJSON($schoolProfile);
            } else {
                $result = APIUtils::getErrorResponse(404);
            }
        }else{
            $result = APIUtils::getErrorResponse(403);
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/person/{personId}/profile/{profileId}",
     * requirements={"personId" = "\d+", "profileId" = "\d+"})
     * @Method({"GET"})
     */
    public function resultPersonProfileAction(Request $request, $personId, $profileId){

        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $personProfile = $this->getResQuery(array('personId' => $personId, 'profileId' => $profileId), 'P.personid = :personId AND PS.profileid = :profileId');

            if ($personProfile) {
                $result = $this->getJSON($personProfile);
            } else {
                $result = APIUtils::getErrorResponse(404);
            }
        }else{
            $result = APIUtils::getErrorResponse(403);
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/person/{personId}",
     * requirements={"personId" = "\d+"})
     * @Method({"GET"})
     */
    public function resultPersonAction($personId){

        $person = $this->getResQuery(array('personId' => $personId), 'P.personid = :personId');

        if ($person) {
            $result = $this->getJSON($person);
        } else {
            $result = APIUtils::getErrorResponse(404);
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/school/{schoolId}",
     * requirements={"schoolId" = "\d+"})
     * @Method({"GET"})
     */
    public function resultSchoolAction(Request $request, $schoolId){

        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $school = $this->getResQuery(array('schoolId' => $schoolId), 'PS.schoolid = :schoolId');

            if ($school) {
                $result = $this->getJSON($school);
            } else {
                $result = APIUtils::getErrorResponse(404);
            }
        }else{
            $result = APIUtils::getErrorResponse(403);
        }

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
    private function getResQuery($parameters, $where){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyResultSurveyPerson = $qb
            ->select("PS.schoolid, Sc.schoolcode, Sc.school, P.personid, P.user, P.name, P.surname, S.surveyid, S.title, S.description, S.active")
            ->addSelect("S.closingdate, S.creationdate, QS.order as orderQ, Ac.actioncode, Q.questionid, Q.question, Sub.subcategory, A.answerid, A.answer, A.comment, OQ.order as orderO, O.optionid, O.option")
            ->from('UNOEvaluacionesBundle:Person ','P')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'P.personid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile ','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:School ','Sc', 'WITH', 'PS.schoolid = Sc.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'SP.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','L', 'WITH', "S.surveyid = L.surveySurveyid AND P.personid = L.personPersonid")
            ->innerJoin('UNOEvaluacionesBundle:Action','Ac', 'WITH', 'L.actionaction = Ac.idaction')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'S.surveyid = QS.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'QS.questionxsurveyId = OQ.questionxsurvey')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionxsurveyId = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','A', 'WITH', "OQ.optionxquestionId = A.optionxquestion AND P.personid = A.personPersonid")
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere("Ac.actioncode  = '004'")
            ->andWhere($where)
            ->setParameters($parameters)
            ->groupBy('PS.schoolid, P.personid, S.surveyid, Q.question, O.optionid')
            ->orderBy( 'PS.schoolid')
            ->addOrderBy('P.personid')
            ->addOrderBy('S.surveyid')
            ->addOrderBy('QS.order')
            ->addOrderBy('O.optionid')
            ->getQuery()
            ->getResult();

        return $_surveyResultSurveyPerson;
    }

    /**
     * @param $array
     * @return mixed
     *
     * crea el json
     */
    private function getJSON($array){

        $schoolArray = array();
        $personArray = array();
        $surveyArray = array();
        $questionArray = array();
        $optionArray = array();

        foreach($array as $value) {
            //schools
            if (!isset($schoolArray['schoolId'][$value['schoolid']]) ) {
                array_push($schoolArray, array(
                        'schoolId' => $value['schoolid'],
                        'schoolCode' => $value['schoolcode'],
                        'school' => $value['school'],
                        'persons' => array()
                    )
                );
            }

            //persons
            if (!isset($schoolArray['personId'][$value['personid']]) ) {
                array_push($personArray, array(
                        'personId' => $value['personid'],
                        'user' => $value['user'],
                        'name' => $value['name'],
                        'surname' => $value['surname'],
                        'schoolId' => $value['schoolid'],
                        'surveys' => array()
                    )
                );
            }

            //surveys
            if (!isset($surveyArray['surveyId'][$value['surveyid']]) ) {
                array_push($surveyArray, array(
                        'surveyId' => $value['surveyid'],
                        'title' => $value['title'],
                        'description' => $value['description'],
                        'active' => $value['active'],
                        'closingDate' => $value['closingdate'],
                        'creationDate' => $value['creationdate'],
                        'schoolId' => $value['schoolid'],
                        'personId' => $value['personid'],
                        'questions' => array()
                    )
                );
            }

            //questions
            if (!isset($questionArray['questionId'][$value['questionid']]) ) {
                array_push($questionArray, array(
                        'orderQ' => $value['orderQ'],
                        'questionId' => $value['questionid'],
                        'question' => $value['question'],
                        'category' => $value['subcategory'],
                        'schoolId' => $value['schoolid'],
                        'personId' => $value['personid'],
                        'surveyId' => $value['surveyid'],
                        'options' => array(),
                        'answers' => array()
                    )
                );
            }

            //options
            if (!isset($optionArray['optionId'][$value['optionid']]) ) {
                array_push($optionArray, array(
                        'orderO' => $value['orderO'],
                        'optionid' => $value['optionid'],
                        'option' => $value['option'],
                        'answer' => $value['answer'],
                        'comment' => $value['comment'],
                        'schoolId' => $value['schoolid'],
                        'personId' => $value['personid'],
                        'surveyId' => $value['surveyid'],
                        'questionId' => $value['questionid'],
                    )
                );
            }
        }

        $schools = array_unique($schoolArray, SORT_REGULAR);
        $persons = array_unique($personArray, SORT_REGULAR);
        $surveys = array_unique($surveyArray, SORT_REGULAR);
        $questions = array_unique($questionArray, SORT_REGULAR);
        $options = array_unique($optionArray, SORT_REGULAR);

        foreach($schools as $valSchool){
            foreach($persons as $valPerson){
                if($valSchool['schoolId'] == $valPerson['schoolId']){
                    foreach ($surveys as $valSurvey) {
                        if ($valPerson['personId'] == $valSurvey['personId'] && $valPerson['schoolId'] == $valSurvey['schoolId']) {
                            foreach($questions as $valQuestion){
                                if ($valSurvey['surveyId'] == $valQuestion['surveyId'] && $valSurvey['personId'] == $valQuestion['personId'] && $valSurvey['schoolId'] == $valQuestion['schoolId']) {
                                    $answer = array();
                                    foreach($options as $valOption){
                                        if ($valQuestion['questionId'] == $valOption['questionId'] && $valQuestion['surveyId'] == $valOption['surveyId'] && $valQuestion['personId'] == $valOption['personId'] && $valQuestion['schoolId'] == $valOption['schoolId']) {
                                            if( !empty($valOption['answer']) ){
                                                array_push($answer, array(
                                                    'optionid' => $valOption['optionid'],
                                                    'answer' => $valOption['answer'],
                                                    'comment' => $valOption['comment']
                                                ));
                                            }
                                            array_push($valQuestion['options'],
                                                array(
                                                    'orderO' => $valOption['orderO'],
                                                    'optionid' => $valOption['optionid'],
                                                    'option' => $valOption['option']
                                                )
                                            );
                                        }
                                    }
                                    array_push($valSurvey['questions'], array(
                                        'orderQ' => $valQuestion['orderQ'],
                                        'questionId' => $valQuestion['questionId'],
                                        'question' => $valQuestion['question'],
                                        'category' => $valQuestion['category'],
                                        'options' => $valQuestion['options'],
                                        'answers' => $answer
                                    ));
                                }
                            }
                            array_push($valPerson['surveys'], array(
                                'surveyId' => $valSurvey['surveyId'],
                                'title' => $valSurvey['title'],
                                'description' => $valSurvey['description'],
                                'active' => $valSurvey['active'],
                                'closingDate' => $valSurvey['closingDate'],
                                'creationDate' => $valSurvey['creationDate'],
                                'questions' => $valSurvey['questions']
                            ));
                        }
                    }
                    array_push($valSchool['persons'],array(
                        'personId' => $valPerson['personId'],
                        'user' => $valPerson['user'],
                        'name' => $valPerson['name'],
                        'surname' => $valPerson['surname'],
                        'surveys' => $valPerson['surveys']
                    ));
                }
            }
        }
        return($valSchool);
    }

    private function getSchool($array){
        $JSON = array();
        foreach($array as $index){


            if( !isset( $JSON[$index['schoolid']]['persons'][$index['personid']] ) ){
                $JSON[$index['schoolid']]['persons'][$index['personid']] = array(
                    'personId' => $index['personid'],
                    'user' => $index['user'],
                    'name' => $index['name'],
                    'surname' => $index['surname'],
                    'surveys' => array()

                );
            }

            if( !isset($JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]) ){
                $JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']] = array(
                    'surveyId' => $index['surveyid'],
                    'title' => $index['title'],
                    'description' => $index['description'],
                    'active' => $index['active'],
                    'closingDate' => $index['closingdate'],
                    'creationDate' => $index['creationdate'],
                    'questions' => array()

                );
            }

            if( !isset($JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]['questions'][$index['questionid']]) ){
                $JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]['questions'][$index['questionid']] = array(
                    'orderQ' => $index['orderQ'],
                    'questionId' => $index['questionid'],
                    'question' => $index['question'],
                    'options' => array(),
                    'answers' => array()

                );
            }

            if( !isset($JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]['questions'][$index['questionid']]['options'][$index['optionid']]) ){
                $JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]['questions'][$index['questionid']]['options'][$index['optionid']] = array(
                    'orderO' => $index['orderO'],
                    'optionid' => $index['optionid'],
                    'option' => $index['option']
                );
            }

            if( !isset($JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]['questions'][$index['questionid']]['answers'][$index['optionid']]) ){
                if( !empty($index['answer']) ){
                    $JSON[$index['schoolid']]['persons'][$index['personid']]['surveys'][$index['surveyid']]['questions'][$index['questionid']]['answers'][$index['optionid']] = array(
                        'optionid' => $index['optionid'],
                        'answer' => $index['answer'],
                        'comment' => $index['comment']
                    );
                }
            }

        }

        return $JSON;

    }

}