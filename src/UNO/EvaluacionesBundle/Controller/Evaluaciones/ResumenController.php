<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 12/10/15
 * Time: 10:14 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('ANSWERED_LOG_CODE', '4');

class ResumenController extends Controller
{
    /**
     *
     * Método principal del controlador responsable de la lógica para mostrar el resumen de una evaluación ya respondida
     *
     * @param Request $request
     * @param $surveyId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request, $surveyId){

        // Validación de sesión activa
        $session = $request->getSession();
        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login',array(
                'redirect' => 'resumen',
                'with' => $surveyId
            ));
        }

        // Autorización de evaluación
        if(!Utils::isSurveyAuthorized($session,$surveyId)) {
            throw new AccessDeniedHttpException('No estás autorizado para visualizar este contenido');
        }

        $personId = $session->get('personIdS');

        $survey = $this->getSurvey($surveyId,$personId);

        if(isset($survey['Error'])){

            throw new NotFoundHttpException('Resumen no encontrado para esta evaluación');
        }

        $categories = array_unique(array_column($survey['persons'][0]['surveys'][0]['questions'],'category'));
        $tasks = $this->getTasksByCategory($survey,$categories);
        $statsbyCat = $this->getStatsByCategory($survey,$categories);
        $statsbyOpt = $this->getStatsByOptions($surveyId,$personId)['global'];


        return $this->render('UNOEvaluacionesBundle:Evaluaciones:resumen.html.twig', array(

            'survey' => $survey['persons'][0]['surveys'][0],
            'date' => $survey['persons'][0]['surveys'][0]['answerDate']['date'],
            'tasks' => $tasks,
            'pie_stats'=> $statsbyOpt,
            'bars_stats' => $statsbyCat
        ));
    }


    private function getSurvey($surveyId, $personId){

        return json_decode(file_get_contents($this->generateUrl('APIResultBySurveyPerson',array(
            'surveyId' => $surveyId,
            'personId' => $personId
        ),true),false),true);
    }

    private function getStatsByCategory($survey,$categories){

        $questions = $survey['persons'][0]['surveys'][0]['questions'];
        $si_serie = array(
            'name' => 'Sí',
            'data' => array()
        );
        $no_serie = array(
            'name' => 'No',
            'data' => array()
        );
        $nose_serie = array(
            'name' => 'No sé',
            'data' => array()
        );

        $statsbyCat = array(
            'categories' => array(),
            'series' => array(),
        );

        foreach($categories as $cat) {

            array_push($statsbyCat['categories'],$cat);
            array_push($si_serie['data'],count(array_filter($questions, function($ar) use($cat){ return ($ar['category'] == $cat AND $ar['answers'][0]['answer'] == 'Sí'); })));
            array_push($no_serie['data'],count(array_filter($questions, function($ar) use($cat){ return ($ar['category'] == $cat AND $ar['answers'][0]['answer'] == 'No'); })));
            array_push($nose_serie['data'],count(array_filter($questions, function($ar) use($cat){ return ($ar['category'] == $cat AND !in_array($ar['answers'][0]['answer'],array('No','Sí'))); })));
        }

        array_push($statsbyCat['series'],$si_serie);
        array_push($statsbyCat['series'],$no_serie);
        array_push($statsbyCat['series'],$nose_serie);

        return $statsbyCat;
    }

    private function getTasksByCategory($survey, $categories) {

        $questions = $survey['persons'][0]['surveys'][0]['questions'];

        $tasks = array();
        foreach($categories as $cat) {

            $tasks[$cat] = array_filter($questions, function($ar) use($cat){ return ($ar['category'] == $cat AND $ar['answers'][0]['answer'] != 'Sí'); });
        }
        return $tasks;
    }

    private function getStatsByOptions($surveyId, $personId){

        return json_decode(file_get_contents($this->generateUrl('APIStatsResultsBySurveyPerson',array(
            'surveyid' => $surveyId,
            'personid' => $personId
        ),true),false),true);
    }
}