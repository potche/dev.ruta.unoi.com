<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class APISurveyController extends Controller{

    public function surveyAction(Request $request){

        return new Response(json_encode('survey',200));

    }

}