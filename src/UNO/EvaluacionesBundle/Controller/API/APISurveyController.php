<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

class APISurveyController extends Controller{

    public function surveyAction(Request $request){

        $response = new JsonResponse();
        $response->setData($this->getBySurvey());

        //$response->setData(Utils::isUserLoggedIn($session) ? $this->getBySurvey(): $this->getErrorResponse('403'));

        return $response;
    }

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

    public function surveybyroleAction(Request $request, $schoollevelid, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getByPersonSchool(null,$schoollevelid, $profileid));

        return $response;
    }

    public function surveybylevelAction(Request $request, $schoollevelid){

        $response = new JsonResponse();
        $response->setData($this->getByPersonSchool(null,$schoollevelid));

        return $response;
    }

    public function surveybyprofileAction(Request $request, $profileid){

        $response = new JsonResponse();
        $response->setData($this->getByPersonSchool(null,null,$profileid));

        return $response;
    }

    public function surveybypersonAction(Request $request, $personid){

        $response = new JsonResponse();
        $response->setData($this->getByPersonSchool(null,null,null,$personid));

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

        return $all ? $this->buildArray($all): $this->getErrorResponse('404');
    }

    /**
     *
     * Función para ejecutar query por escuela
     *
     * @param $schoolId
     * @return array
     */

    protected function getByPersonSchool($schoolid = null, $schoollevelid = null, $profileid = null, $personid = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $condition = "su.surveyid > 1";

        //Se agregan las condiciones segun sea el caso

        $condition = $schoolid != null ? $condition.' AND ps.schoolid = '.$schoolid : $condition;
        $condition = $schoollevelid != null ? $condition.' AND sxp.schoollevelid = '.$schoollevelid : $condition;
        $condition = $profileid != null ? $condition.' AND sxp.profileProfileid = '.$profileid : $condition;
        $condition = $personid != null ? $condition.' AND ps.personid = '.$personid : $condition;


        $bySchool = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, ps.profileid as perfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sc','WITH','sc.subcategoryid = q.subcategorySubcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->where($condition)
            ->groupBy('id, titulo, nivel, perfil, pregunta, categoria, oxq.optionOptionid')
            ->getQuery()
            ->getResult();

        return $bySchool ? $this->buildArray($bySchool): $this->getErrorResponse('404');
    }

    private function getByPerson($personid){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $byPerson = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, ps.profileid as perfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion, COALESCE(a.actioncode,'0') AS actioncode")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.surveySurveyid = su.surveyid AND l.personPersonid = :personId')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','l.actionaction = a.idaction')
            ->where('ps.personid = :personId')
            ->groupBy('id, titulo, nivel, perfil, pregunta, categoria, oxq.optionOptionid, a.actioncode')
            ->setParameter('personId',$personid)
            ->getQuery()
            ->getResult();

        return $byPerson ? $this->buildArray($byPerson) : $this->getErrorResponse('404');
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
                    'actioncode' => !isset($s['actioncode']) ? '' : $s['actioncode'],
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

    private function getErrorResponse($code){

        $reason = '';

        switch($code){

            case '404':
                $reason = 'Resource not exists';
                break;
            case '403':
                $reason = 'Not authorized, please login';
                break;
            default:
                $reason = 'Unknown error, try again later';
                break;
        }

        return array(
            'Error' => $reason,
            'Code' => $code
        );
    }
}