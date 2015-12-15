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
            //return $this->redirectToRoute('login');
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



        // Una vez que validamos los escenarios posibles, ejecutamos la consulta para traer las respuestas del usuario
        //$results = $this->getSurveyResults($surveyId,$personId);
        //$categories = array_unique(array_column($results,'subcategory'));
        //$tasks = $this->getTasksByCategory($results,$categories);
        //$options = $this->getAnswerOptions($surveyId);
        //$pie_stats = $this->getStatsByAnswer($results,$options);
        //$bars_stats = $this->getStatsByCategory($categories, $options, $results);

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:resumen.html.twig', array(

            'survey' => $survey['persons'][0]['surveys'][0],
            'date' => $survey['persons'][0]['surveys'][0]['answerDate']['date'],
            //'title' => $details[0]['title'],
            //'date' => $details[0]['date']->format('j/M/Y \@ g:i a'),
            //'results' => $results,
            //'categories' => $categories,
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

    /**
     * Método que obtiene y devuelve título de evaluación, fecha y hora en que respondió el usuario
     *
     *
     * @param $personId
     * @param $surveyId
     * @param $action
     * @return mixed
     * @author julio
     */

    private function getSurveyLog($personId, $surveyId, $action){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $eval = $qb->select('su.title', 'log.date')
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->leftJoin('UNOEvaluacionesBundle:Log','log', 'WITH', 'su.surveyid = log.surveySurveyid')
            ->where('log.surveySurveyid = :surveyId')
            ->andWhere('log.personPersonid = :personId')
            ->andWhere('log.actionaction = :logcode')
            ->orderBy('log.date')
            ->setMaxResults(1)
            ->setParameters(array(
                'personId' => $personId,
                'surveyId' => $surveyId,
                'logcode' => $action,
            ))
            ->getQuery()
            ->getResult();

        return $eval;
    }

    /**
     *
     * Método que obtiene y devuelve:
     * orden, pregunta, categoría, respuesta, comentario de los reactivos de una evaluación
     *
     * @param $surveyId
     * @param $personId
     * @return mixed
     */


    private function getSurveyResults($surveyId, $personId) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $results = $qb->select('qxs.order', 'qu.question', 'sub.subcategory','ans.answer','ans.comment')
            ->from('UNOEvaluacionesBundle:Answer','ans')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq', 'WITH', 'ans.optionxquestion = oxq.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs', 'WITH', 'oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','qu', 'WITH', 'qxs.questionQuestionid = qu.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sub', 'WITH', 'qu.subcategorySubcategoryid = sub.subcategoryid')
            ->where('qxs.surveySurveyid = :surveyId')
            ->andWhere('ans.personPersonid = :personId')
            ->orderBy('qxs.order')
            ->setParameters(array(
                'personId' => $personId,
                'surveyId' => $surveyId,
            ))
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     *
     * Obtengo las opciones de las preguntas para manejar las estadísticas
     *
     * @param $surveyId
     * @return mixed
     */

    private function getAnswerOptions($surveyId) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $opts = $qb->select('opc.option as name')
            ->from('UNOEvaluacionesBundle:Option','opc')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq', 'WITH', 'opc.optionid = oxq.optionOptionid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs', 'WITH', 'oxq.questionxsurvey = qxs.questionxsurveyId')
            ->where('qxs.surveySurveyid = :surveyId')
            ->groupBy('opc.option')
            ->orderBy('oxq.order')
           ->setParameters(array(
                'surveyId' => $surveyId
            ))
            ->getQuery()
            ->getResult();

        return $opts;
    }

    /**
     *
     * Obtengo las estadísticas por respuestas, (funciona con cualquier tipo de respuesta de opción multiple)
     *
     * @param $results
     * @param $options
     * @return array
     */
    private function getStatsByAnswer($results, $options){

        $total = count(array_column($results,'answer'));
        $countByAnswer = array_count_values(array_column($results,'answer'));
        $pieStats = array();

        foreach($options as $k => $val){

            array_push($pieStats,array(
                'name' => $val['name'],
                'y' => ($total > 0 && array_key_exists($val['name'],$countByAnswer) ? round((($countByAnswer[$val['name']] * 100) / $total),2) : 0)
            ));
        }
        return $pieStats;
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