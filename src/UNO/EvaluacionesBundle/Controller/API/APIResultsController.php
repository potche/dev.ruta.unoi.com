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
            'profileId' => $profileId,
            'schoolId' => $schoolId,
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
}