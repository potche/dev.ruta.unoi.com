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
use Symfony\Component\HttpFoundation\Response;

class ListarController extends Controller
{

    /**
     * Controlador que maneja la lista de evaluaciones respondidas y por responder del usuario
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @version 0.3.0
     * @author jbravob julio@dsindigo.com
     * @copyright Sistema UNO Internacional. 2015
     */
    public function indexAction(Request $request) {

        $session = $request->getSession();
        // Si no se tiene iniciada una sesión, se redirige al login
        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login',array(
                'redirect' => 'listar',
                'with' => 'none'
            ));
        }

        $personID = $session->get('personIdS');
        $response = json_decode(file_get_contents($this->generateUrl('APISurveysPerson',array('personid'=>$personID),true), false), true);

        if(!$response['Error']){
            $countToBeAnswered = array_count_values(array_column($response,'actioncode'))['0'];
            $statistics = Utils::fetchStats(count($response),$countToBeAnswered);

        }else{
            $response = '';
            $statistics = Utils::fetchStats(count($response),0);
        }

        return $this->render('@UNOEvaluaciones/Evaluaciones/listar.html.twig', array(

            'surveyList' => $response,
            'stats' => $statistics,
        ));
    }
}