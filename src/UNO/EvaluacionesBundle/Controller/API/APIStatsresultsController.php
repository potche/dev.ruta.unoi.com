<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 09/12/15
 * Time: 12:34 PM
 */

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class APIStatsresultsController extends Controller {

    public function allstatsAction(Request $request){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoolAction(Request $request, $schoolid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbypersonAction(Request $request, $personid){

        $response = new JsonResponse();
        $response->setData();

        return $response;

    }

    public function statsbysurveyAction(Request $request, $surveyid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbysurveyschoolAction(Request $request, $surveyid, $schoolid){

        $response = new JsonResponse();
        $response->setData();

        return $response;

    }

    public function statsbysurveypersonAction(Request $request, $surveyid, $personid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoollevelAction(Request $request, $schoolid, $levelid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoolprofileAction(Request $request, $schoolid, $profileid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function statsbyschoolroleAction(Request $request, $schoolid, $levelid, $profileid){

        $response = new JsonResponse();
        $response->setData();

        return $response;
    }

    public function getAll(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        //$all = $qb->select("su.surveyid as id, su.title as titulo")







    }







}