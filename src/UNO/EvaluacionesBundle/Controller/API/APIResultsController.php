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

    /**
     * @Route("/result/survey/{surveyId}/person/{personId}"),
     * requirements={"surveyId" = "\d+", "personId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSurveyPersonAction($surveyId, $personId){
        $result = array(
            'hola' => 'resultSurveyPerson',
            'surveyId' => $surveyId,
            'personId' => $personId,
            );

        print_r($this->getResQuery());


        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/result/survey/{surveyId}/school/{schoolId}"),
     * requirements={"surveyId" = "\d+", "schoolId" = "\d+"}
     * @Method({"GET"})
     */
    public function resultSurveySchoolAction($surveyId, $schoolId){
        $result = array(
            'hola' => 'resultSurveySchoolId',
            'surveyId' => $surveyId,
            'schoolId' => $schoolId,
        );
        #-----envia el arreglo a JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
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

    private function getResQuery(){

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
            ->andWhere('Sc.schoolid = 1145')
            ->andWhere('P.personid = 1159480')
            ->andWhere('S.surveyid = 3')
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
}