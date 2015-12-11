<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 13/10/15
 * Time: 11:35 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminController extends Controller {


    /**
     * Controlador principal que maneja la vista del administrador, y muestra la opción para crear una evaluación
     *
     * @param Request $request
     * @return Response
     * @author julio
     * @version 0.2.0
     */
    public function indexAction(Request $request){

        $session = $request->getSession();

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }

        $stats = $this->getStats();
        $surveys = $this->getSurveysWithProfiles($stats);

        // Obtenemos las estadísticas generales de avance para mostrarlas en la gráfica de pastel

        return $this->render('UNOEvaluacionesBundle:Crear:menueval_admin.html.twig', array(
            'surveylist' => $surveys,
            'stats_general' => $stats['general'],
        ));
    }

    /**
     *
     * Método que obtiene a través de una consulta a la bd las evaluaciones registradas,
     * y agrega el nivel de avance de cada una de ellas a través del parámetro stats.
     *
     * @param $stats
     * @return array
     * @author julio
     * @version 0.2.0
     */
    private function getSurveysWithProfiles($stats){

        //Consulta a la bd para extraer evaluaciones con sus respectivos perfiles y niveles asociados (sin agrupar)
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $surveysWithProfiles = $qb->select("su.surveyid, su.title, su.creationdate, su.createdby, su.active, su.closingdate, pr.profile, sl.schoollevel")
            ->from('UNOEvaluacionesBundle:Survey', 'su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr', 'WITH','pr.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','sl', 'WITH','sl.schoollevelid = sxp.schoollevelid')
            ->where('sxp.surveySurveyid > 1')
            ->getQuery()
            ->getResult();

        //Obtengo las claves de las evaluaciones, para recorrer el arreglo de las consultas y asignar campos
        $surveys = array_flip(array_unique(array_column($surveysWithProfiles,'surveyid')));

        //Agrupo las evaluaciones con sus niveles y formateo los campos que se muestran en la vista
        foreach ($surveys as $surveyid => $survey) {

            $surveys[$surveyid] = array();
            $surveys[$surveyid]['profiles'] = array();

            //Recorro el arreglo de la consulta para asignar a cada evaluacion los campos que corresponden y sus perfiles
            foreach($surveysWithProfiles as $swp) {

                if($swp['surveyid'] == $surveyid && !array_key_exists('surveyid',$surveys[$surveyid])) {

                    $surveys[$surveyid]['id'] = $swp['surveyid'];
                    $surveys[$surveyid]['title'] = $swp['title'];
                    $surveys[$surveyid]['createdon'] = $swp['creationdate']->format('j/M/Y');
                    $surveys[$surveyid]['createdby'] = $swp['createdby'];
                    $surveys[$surveyid]['closingdate'] = $swp['closingdate']->format('j/M/Y \@ g:i a');
                    $surveys[$surveyid]['progress'] = $stats['bySurvey'][$surveyid]['completado'];
                    $surveys[$surveyid]['completed'] = $stats['bySurvey'][$surveyid]['respondido'];
                    $surveys[$surveyid]['expected'] = $stats['bySurvey'][$surveyid]['esperado'];
                    $surveys[$surveyid]['active'] = $swp['active'];

                    if(!array_key_exists($swp['profile'],$surveys[$surveyid]['profiles'])) {

                        $surveys[$surveyid]['profiles'][$swp['profile']] = array();
                    }

                    array_push($surveys[$surveyid]['profiles'][$swp['profile']],$swp['schoollevel']);
                }
                elseif( $swp['surveyid'] == $surveyid && array_key_exists('surveyid',$surveys[$surveyid])) {

                    array_push($surveys[$surveyid]['profiles'][$swp['profile']],$swp['schoollevel']);
                }
            }
        }
        return $surveys;
    }

    /**
     * Método para obtener las estadísticas de cumplimiento a nivel evaluación y global.
     * @return array
     */
    private function getStats(){

        $data = array();
        $em = $this->getDoctrine()->getManager();

        //Consulta para obtener el numero esperado de respuestas por evaluacion
        $qb = $em->createQueryBuilder();
        $expectedBySurvey = $qb->select("su.surveyid, count(distinct p.personid) as expectedNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->where('sxp.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->groupBy('sxp.surveySurveyid')
            ->getQuery()
            ->getResult();

        //Consulta para obtener el numero de respuestas actuales por evaluacion
        $qb = $em->createQueryBuilder();
        $answeredBySurvey= $qb->select("su.surveyid, count(distinct ps.personid) as answeredNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','log','WITH','ps.personid = log.personPersonid AND sxp.surveySurveyid = log.surveySurveyid')
            ->where('log.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->andWhere('log.actionaction = 4')
            ->groupBy('sxp.surveySurveyid')
            ->getQuery()
            ->getResult();

        //Combino los valores obtenidos y calculo el avance por cada evaluación hacia el arreglo de salida
        foreach($expectedBySurvey as $k => $expectedElem) {

            $avance = 0.0;
            $esperado = 0;
            $respondido = 0;

            foreach ($answeredBySurvey as $answeredElement) {

                if($expectedElem['surveyid'] == $answeredElement['surveyid']) {

                    $esperado = $expectedElem['expectedNum'];
                    $respondido = $answeredElement['answeredNum'];
                    $avance  = ($esperado > 0 ? round(($respondido * 100 ) / $esperado,2) : 0);
                }
            }
            $data['bySurvey'][$expectedElem['surveyid']]['avance'] = $avance;
            $data['bySurvey'][$expectedElem['surveyid']]['esperado'] = $esperado;
            $data['bySurvey'][$expectedElem['surveyid']]['respondido'] = $respondido;
        }

        //Calculo el nivel general de cumplimiento a partir de la suma de los valores obtenidos en las consultas

        $data['general'] = array();
        $esperadoGlobal = array_sum(array_column($expectedBySurvey,'expectedNum'));
        $respondidoGlobal = array_sum(array_column($answeredBySurvey,'answeredNum'));
        $avanceGlobal =  ($esperadoGlobal > 0 ? round(($respondidoGlobal * 100) / $esperadoGlobal,2) : 0);

        //Construyo el arreglo 'general' para hacer el render a la gráfica con la estructura requerida por Highcharts

        array_push($data['general'],array(
            'name' => 'Completado',
            'y' => $avanceGlobal
            ));

        array_push($data['general'],array(
            'name' => 'Pendiente',
            'y' => 100 - $avanceGlobal
        ));

        return $data;
    }

    private function getStatistics() {

        //return json_decode(file_get_contents($this->generateUrl('APIStatsProgress',true), false), true);
        return json_decode(file_get_contents($this->generateUrl('APIStatsProgress',array(),true),false),true);
    }

    private function getSurveys(){

        return json_decode(file_get_contents($this->generateUrl('APIStatsProgress',array(),true),false),true);

    }

    public function setSurveyStatusAction(Request $request) {

        $surveyId = $request->request->get('surveyid');
        $status = $request->request->get('surveyStatus');

        if (!$surveyId || !$status) {

            $response = json_encode(array('message' => 'Petición malformada'));
            return new Response($response, 500, array(
                'Content-Type' => 'application/json'
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $survey = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Survey')
            ->findOneBy(array(
                'surveyid' => $surveyId
            ));

        $survey->setActive($status === 'true');
        $em->flush();

        $response = json_encode(array('message' => 'Se ha actualizado con exito'));
        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }
}