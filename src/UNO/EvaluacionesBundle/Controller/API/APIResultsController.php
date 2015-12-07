<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 01/12/15
 * Time: 3:05 PM
 */

/**
 * @Route("/api/v0")
 */
class APIResultsController extends Controller{

    /**
     * @Route("/results")
     * @Method({"GET"})
     */
    public function resultsAction(){
        $result = array('hola' => 'results');


        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function getResQueryAll(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyResults = $qb
            ->select("PS.schoolid, Sc.schoolcode, Sc.school, P.personid, P.user, P.name, P.surname, S.surveyid, S.title, S.description, S.active")
            ->addSelect("S.closingdate, S.creationdate, QS.order, Ac.actioncode, Q.questionid, Q.question, A.answerid, A.answer, A.comment, OQ.order, O.optionid, O.option")
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
            ->leftJoin('UNOEvaluacionesBundle:Answer','A', 'WITH', "OQ.optionxquestionId = A.optionxquestion AND P.personid = A.personPersonid")
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere("Ac.actioncode  = '004'")
            ->andWhere('Sc.schoolid = :schoolId')
            ->andWhere('P.personid = :personId')
            ->andWhere('S.surveyid = :surveyId')
            ->setParameter('schoolId',1145)
            ->setParameter('personId',1159480)
            ->setParameter('surveyId',3)
            ->groupBy('PS.schoolid, P.personid, S.surveyid, Q.question, O.optionid')
            ->orderBy( 'PS.schoolid')
            ->addOrderBy('P.personid')
            ->addOrderBy('S.surveyid')
            ->addOrderBy('QS.order')
            ->addOrderBy('O.optionid')
            ->getQuery()
            ->getResult();

        return $_surveyResults;
    }

    /**
     * @Route("/result/survey/{surveyId}/person/{personId}"),
     * requirements={"surveyId" = "\d+", "personId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSurveyPersonAction($surveyId, $personId){

        $surveyPerson = $this->getResQuerySurveyPerson($surveyId, $personId);
        $result = $this->createJSON($surveyPerson);


        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function getResQuerySurveyPerson($surveyId, $personId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyResultSurveyPerson = $qb
            ->select("PS.schoolid, Sc.schoolcode, Sc.school, P.personid, P.user, P.name, P.surname, S.surveyid, S.title, S.description, S.active")
            ->addSelect("S.closingdate, S.creationdate, QS.order, Ac.actioncode, Q.questionid, Q.question, A.answerid, A.answer, A.comment, OQ.order, O.optionid, O.option")
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
            ->leftJoin('UNOEvaluacionesBundle:Answer','A', 'WITH', "OQ.optionxquestionId = A.optionxquestion AND P.personid = A.personPersonid")
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere("Ac.actioncode  = '004'")
            ->andWhere('P.personid = :personId')
            ->andWhere('S.surveyid = :surveyId')
            ->setParameters(array('personId' => $personId, 'surveyId' => $surveyId))
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
     * @Route("/result/survey/{surveyId}/school/{schoolId}"),
     * requirements={"surveyId" = "\d+", "schoolId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSurveySchoolAction(Request $request, $surveyId, $schoolId){
        $surveyPerson = $this->getResQuerySurveySchool($surveyId, $schoolId);
        $result = $this->createJSON($surveyPerson);

        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;

    }

    private function getResQuerySurveySchool($surveyId, $schoolId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyResultSurveyPerson = $qb
            ->select("PS.schoolid, Sc.schoolcode, Sc.school, P.personid, P.user, P.name, P.surname, S.surveyid, S.title, S.description, S.active ")
            ->addSelect("S.closingdate, S.creationdate, QS.order as orderQ, Ac.actioncode, Q.questionid, Q.question, A.answerid, A.answer, A.comment, OQ.order as orderO, O.optionid, O.option")
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
            ->leftJoin('UNOEvaluacionesBundle:Answer','A', 'WITH', "OQ.optionxquestionId = A.optionxquestion AND P.personid = A.personPersonid")
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere("Ac.actioncode  = '004'")
            ->andWhere('PS.schoolid = :schoolId')
            ->andWhere('S.surveyid = :surveyId')
            ->setParameters(array('schoolId' => $schoolId, 'surveyId' => $surveyId))
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
     * @Route("/result/school/{schoolId}/profile/{profileId}"),
     * requirements={"schoolId" = "\d+", "profileId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSchoolProfileAction($schoolId, $profileId){
        $result = array(
            'hola' => 'resultSchoolProfile',
            'schoolId' => $schoolId,
            'profileId' => $profileId,
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/person/{personId}/profile/{profileId}"),
     * requirements={"personId" = "\d+", "profileId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultPersonProfileAction($personId, $profileId){
        $result = array(
            'hola' => 'resultSchoolProfile',
            'personId' => $personId,
            'profileId' => $profileId,
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/survey/{surveyId}"),
     * requirements={"surveyId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSurveyAction($surveyId){
        $result = array(
            'hola' => 'resultSurvey',
            'surveyId' => $surveyId
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/person/{personId}"),
     * requirements={"personId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultPersonAction($personId){
        $result = array(
            'hola' => 'resultPerson',
            'personId' => $personId
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/school/{schoolId}"),
     * requirements={"schoolId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSchoolAction($schoolId){
        $result = array(
            'hola' => 'resultSchool',
            'schoolId' => $schoolId
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/profiles")
     *
     */
    public function resultProfilesAction(Request $request){
        $result = array(
            'hola' => 'resultProfiles',
            'profiles' => $request->getContent()
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/profile/{profileId}"),
     * requirements={"profileId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultProfileAction($profileId){
        $result = array(
            'hola' => 'resultProfile',
            'profileId' => $profileId
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function createJSON($array){
        $jsonArray = array();

        //$schools = $this->getSchool($array);
        $schools = $this->getJSON($array);

        array_push($jsonArray, $schools);
        return $jsonArray;
    }

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
        //print_r($schools);
        $persons = array_unique($personArray, SORT_REGULAR);
        //print_r($persons);
        $surveys = array_unique($surveyArray, SORT_REGULAR);
        //print_r($surveys);
        $questions = array_unique($questionArray, SORT_REGULAR);
        //print_r($questions);
        $options = array_unique($optionArray, SORT_REGULAR);
        //print_r($options);

        //$JSON = array();
        foreach($schools as $valSchool){
            //array_push($JSON, $valSchool);
            foreach($persons as $valPerson){
                if($valSchool['schoolId'] == $valPerson['schoolId']){
                    foreach ($surveys as $valSurvey) {
                        if ($valPerson['personId'] == $valSurvey['personId'] && $valPerson['schoolId'] == $valSurvey['schoolId']) {
                            foreach($questions as $valQuestion){
                                if ($valSurvey['surveyId'] == $valQuestion['surveyId'] && $valSurvey['personId'] == $valQuestion['personId'] && $valSurvey['schoolId'] == $valQuestion['schoolId']) {
                                    foreach($options as $valOption){
                                        if ($valQuestion['questionId'] == $valOption['questionId'] && $valQuestion['surveyId'] == $valOption['surveyId'] && $valQuestion['personId'] == $valOption['personId'] && $valQuestion['schoolId'] == $valOption['schoolId']) {
                                            array_push($valQuestion['options'], array(
                                                'orderO' => $value['orderO'],
                                                'optionid' => $value['optionid'],
                                                'option' => $value['option'],
                                                'answer' => $value['answer'],
                                                'comment' => $value['comment']
                                            ));
                                        }
                                    }
                                    //print_r($valQuestion['options']);
                                    array_push($valSurvey['questions'], array(
                                        'orderQ' => $value['orderQ'],
                                        'questionId' => $value['questionid'],
                                        'question' => $value['question'],
                                        'options' => $valQuestion['options'],
                                        'answers'
                                    ));
                                }
                            }
                            array_push($valPerson['surveys'], array(
                                'surveyId' => $value['surveyid'],
                                'title' => $value['title'],
                                'description' => $value['description'],
                                'active' => $value['active'],
                                'closingDate' => $value['closingdate'],
                                'creationDate' => $value['creationdate'],
                                'questions' => $valSurvey['questions']
                            ));
                        }
                    }
                    array_push($valSchool['persons'],array(
                        'personId' => $value['personid'],
                        'user' => $value['user'],
                        'name' => $value['name'],
                        'surname' => $value['surname'],
                        'surveys' => $valPerson['surveys']
                    ));
                }
            }
        }

        return($valSchool);

    }

    private function getSchool($array){
        $schoolArray = array();
        $personArray = array();
        $JSON = array();
        $i = 0;
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

    private function searchForId($index, $id, $array) {
        foreach ($array as $key => $val) {
            if ($val[$index] === $id) {
                return true;
            }
        }
        return false;
    }
}