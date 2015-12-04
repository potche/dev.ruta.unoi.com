<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

class APISurveyController extends Controller{

    /**
     * Endpoints
     */

    /**
     * Endpoint que devuleve todas las evaluaciones
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function surveyAction(Request $request){

        // $session = $request->getSession();

        /*if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }*/

        $response = new JsonResponse();
        $response->setData($this->getBySurvey());

        return $response;
    }

    /**
     * Función que devuleve una evaluación dado un id de evaluación
     *
     * @param Request $request
     * @param $surveyid
     * @return JsonResponse
     * @throws \Exception
     */

    public function surveybyidAction(Request $request, $surveyid){

        $response = new JsonResponse();
        $response->setData($this->getBySurvey($surveyid));

        return $response;
    }

    public function surveysbyschoolAction(Request $request, $schoolid){

        $response = new JsonResponse();
        $response->setData($this->getByPersonSchool($schoolid));

        return $response;
    }

    /**
     * Funciones de consulta
     */

    /**
     *
     * Función para ejecutar query por evaluacion
     *
     * @param null $surveyid
     * @return array
     */

    protected function getBySurvey($surveyid = null) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $condition = $surveyid != null ? 'su.surveyid = '.$surveyid : 'su.surveyid > 1';

        $all = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, p.profileid as perfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','p','WITH','p.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sc','WITH','sc.subcategoryid = q.subcategorySubcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->where($condition)
            ->groupBy('id, titulo, nivel, perfil, pregunta, categoria, oxq.optionOptionid')
            ->getQuery()
            ->getResult();

        return $this->buildArray($all);
    }

    /**
     *
     * Función para ejecutar query por escuela
     *
     * @param $schoolId
     * @return array
     */

    protected function getByPersonSchool($schoolid = null, $personid =null, $surveyid = null, $schoollevelid = null, $profileid = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $bySchool = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, ps.profileid as perfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sc','WITH','sc.subcategoryid = q.subcategorySubcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->where('ps.schoolid = :schoolId')
            ->groupBy('id, titulo, nivel, perfil, pregunta, categoria, oxq.optionOptionid')
            ->setParameter('schoolId',$schoolid)
            ->getQuery()
            ->getResult();

        return ($this->buildArray($bySchool));
    }

    /**
     * Funciones de tratamiento de información
     */

    /**
     *
     *Función para construir arreglo a partir de los resultados de las consultas de evaluaciones
     * @param $query
     * @return array
     */

    private function buildArray($query) {

        $surveys = array();
        foreach ($query as $s) {

            if(!isset($surveys[$s['id']])){

                $surveys[$s['id']] = array(
                    'id' => $s['id'],
                    'titulo' => $s['titulo'],
                    'activa' => $s['activa'],
                    'creada' => $s['creada']->format('U'),
                    'fechacierre' => $s['fechacierre']->format('U'),
                    'creadapor' => $s['creadapor'],
                    'preguntas' => array(),
                    'roles' => array()
                );
            }

            if(!isset($surveys[$s['id']]['roles'][$s['nivel']])) {

                $surveys[$s['id']]['roles'][$s['nivel']] = array();
            }

            if(!in_array($s['perfil'],$surveys[$s['id']]['roles'][$s['nivel']])) {

                array_push($surveys[$s['id']]['roles'][$s['nivel']],$s['perfil']);
            }

            if(!isset($surveys[$s['id']]['preguntas'][$s['numpregunta']])) {

                $surveys[$s['id']]['preguntas'][$s['numpregunta']] = array(
                    'numpregunta' => $s['numpregunta'],
                    'pregunta' => $s['pregunta'],
                    'opciones' => array(),
                );
            }

            if(!isset($surveys[$s['id']]['preguntas'][$s['numpregunta']]['opciones'][$s['numopcion']])){

                $surveys[$s['id']]['preguntas'][$s['numpregunta']]['opciones'][$s['numopcion']] =
                    array(
                        'optionxquestionid' => $s['optionxquestionId'],
                        'opcion' => $s['opcion'],
                    );
            }
        }
        return $surveys;
    }
}