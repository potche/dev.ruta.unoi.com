<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 01/12/15
 * Time: 3:05 PM
 */
class APIResultsController extends Controller
{

    public function resultsAction(Request $request){

        return new Response(json_encode('survey',200));

    }

}