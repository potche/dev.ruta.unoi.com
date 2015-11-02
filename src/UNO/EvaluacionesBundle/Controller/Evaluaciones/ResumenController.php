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
            return $this->redirectToRoute('login');
        }

        // Autorización de evaluación
        if(!Utils::isSurveyAuthorized($session,$surveyId)) {
            throw new AccessDeniedHttpException('No estás autorizado para visualizar este contenido');
        }

        $personId = $session->get('personIdS');
        $details = $this->getSurveyLog($personId,$surveyId,ANSWERED_LOG_CODE);

        // Validamos contra el log que ya haya respondido (manejando el escenario donde el usuario ingresa escribiendo la ruta)
        if(empty($details)) {

            throw new NotFoundHttpException('Resumen no encontrado para esta evaluación');
        }

        // Una vez que validamos los escenarios posibles, ejecutamos la consulta para traer las respuestas del usuario
        $results = $this->getSurveyResults($surveyId,$personId);
        $categories = array_unique(array_column($results,'subcategory'));
        $tasks = $this->getTasksByCategory($results,$categories);
        $options = $this->getAnswerOptions($surveyId);
        $pie_stats = $this->getStatsByAnswer($results,$options);
        $bars_stats = $this->getStatsByCategory($categories, $options, $results);

        /**
         * ToDo: pasar estadística de barras a vista
         */

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:resumen.html.twig', array(
            'title' => $details[0]['title'],
            'date' => $details[0]['date']->format('j/M/Y \@ g:i a'),
            'results' => $results,
            'categories' => $categories,
            'tasks' => $tasks,
            'pie_stats'=> $pie_stats,
            'bars_stats' => $bars_stats
        ));
    }

    /**
     * Método que obtiene y devuelve título de evaluación, fecha y hora en que respondió el usuario
     *
     *
     * @param $personId
     * @param $surveyId
     * @param $action
     * @return mixed
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

    private function getStatsByCategory($categories, $options, $results){
        /**
         * ToDo: implementar formato de estadisticas por categoria (barras)
         */
        $siStr = '';
        $noStr = '';
        $noseStr = '';
        foreach($categories as $valCat){
            $si = 0;
            $no = 0;
            $nose = 0;
            foreach($results as $valRes){
                if($valCat == $valRes['subcategory']) {
                    switch ($valRes['answer']):
                        case 'Sí':
                            $si ++;
                            break;
                        case 'No':
                            $no ++;
                            break;
                        default:
                            $nose ++;
                    endswitch;
                }
            }
            $siStr .= $si.',';
            $noStr .= $no.',';
            $noseStr .= $nose.',';
        }

        return '["'.implode('","',$categories).'"]|'.
        '[{"name": "Sí", "data": ['.trim($siStr,",").'], "stack": "good"},'.
        '{"name": "No", "data": ['.trim($noStr,",").'], "stack": "bad"},'.
        '{"name": "No sé", "data": ['.trim($noseStr,",").'], "stack": "bad"}]';
    }

    private function getTasksByCategory($results, $categories) {

        $tasks = array();
        foreach($categories as $cat) {

            $tasks[$cat] = array();
            foreach($results as $r) {

                if(strcasecmp($cat,$r['subcategory']) == 0 && in_array(strtolower($r['answer']),array('no', 'no sé'))) {

                    array_push($tasks[$cat],$r['question']);
                }
            }
        }
        return $tasks;
    }
}