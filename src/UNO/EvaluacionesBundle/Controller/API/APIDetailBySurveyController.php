<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class APIDetailBySurveyController extends Controller{

    /**
     *
     * Esta función despacha a la ruta /api/v0/detail/{surveyId}/{schoolId}
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function detailAction(Request $request){

        $params = $request->attributes->all()['_route_params'];
        $response = new JsonResponse();
        $response->setData($this->getByParams($params));
        return $response;
    }

    private function getByParams($params){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $all = $qb->select("su.surveyid as id, su.title as titulo, qxs.order as ord, q.question as pregunta, o.option as opcion, p.personid as pid, CONCAT(p.name,' ',p.surname) as nombre, ans.comment as comentario")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','su.surveyid = qxs.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','ans','WITH','ans.optionxquestion = oxq.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ans.personPersonid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.personid = p.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid AND sxp.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','l', 'WITH','l.personPersonid = ps.personid AND l.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Action','a', 'WITH','a.idaction = l.actionaction AND a.idaction = 4')
            ->where('su.surveyid = '.$params['surveyId'])
            ->andWhere('ps.schoolid = '.$params['schoolId'])
            ->groupBy('ord, pregunta, opcion, pid')
            ->orderBy('ord, opcion')
            ->getQuery()
            ->getResult();

        return $all ? $this->parseResults($all) : APIUtils::getErrorResponse('404');
    }

    /**
     *
     * Parseo de resultados de query
     *
     * @param $results
     * @return array
     */

    private function parseResults($results){

        $options = ['Sí','No','No sé'];
        $numQuestions = array_unique(array_column($results,'ord'));
        $parsed = array(
            'id'=>$results[0]['id'],
            'titulo'=>$results[0]['titulo'],
            'preguntas'=>array()
        );

        foreach($numQuestions as $num){

            $numRes = array_filter($results, function($ar) use($num){ return ($ar['ord'] == $num); });
            $orden = array_unique(array_column($numRes,'ord'));
            $pregunta = array_unique(array_column($numRes,'pregunta'));

            $pregunta = array(
                'orden' =>  $orden[0],
                'pregunta' =>  $pregunta[0],
                'opciones' => array()
            );

            $p = array_push($parsed['preguntas'],$pregunta);

            foreach($options as $o){

                $optRes = array_filter($numRes, function($ar) use($o){ return ($ar['opcion'] == $o); });
                $personas = array();

                foreach ($optRes as $per){

                    array_push($personas, array('id'=>$per['pid'],'nombre'=>$per['nombre'], 'comentario' => $per['comentario']));
                }

                $opcion = array(
                    'opcion' => $o,
                    'personas' => $personas
                );

                array_push($parsed['preguntas'][$p-1]['opciones'],$opcion);
            }
        }

        return $parsed;
    }

    /**
     *
     * Esta función despacha a la ruta /api/v0/detail/{surveyId}
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function detailForAdminGrlAction(Request $request){

        $params = $request->attributes->all()['_route_params'];
        $response = new JsonResponse();
        $response->setData($this->getCountResponse($params));
        return $response;
    }

    private function getCountResponse($params){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQuerybuilder();

        $all = $qb->select("su.surveyid as id, su.title as titulo, qxs.order as ord, q.question as pregunta, o.option as opcion, COUNT(distinct(p.personid)) as suma")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','su.surveyid = qxs.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','ans','WITH','ans.optionxquestion = oxq.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ans.personPersonid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.personid = p.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid AND sxp.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','l', 'WITH','l.personPersonid = ps.personid AND l.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Action','a', 'WITH','a.idaction = l.actionaction AND a.idaction = 4')
            ->where('su.surveyid = '.$params['surveyId'])
            ->groupBy('ord, pregunta, opcion')
            ->orderBy('ord, opcion')
            ->getQuery()
            ->getResult();

        return $all ? $this->parseCountResults($all) : APIUtils::getErrorResponse('404');
    }

    /**
     *
     * Parseo de resultados de query
     *
     * @param $results
     * @return array
     */

    private function parseCountResults($results){

        $options = ['Sí','No','No sé'];
        $numQuestions = array_unique(array_column($results,'ord'));
        $parsed = array(
            'id'=>$results[0]['id'],
            'titulo'=>$results[0]['titulo'],
            'preguntas'=>array()
        );

        foreach($numQuestions as $num){

            $numRes = array_filter($results, function($ar) use($num){ return ($ar['ord'] == $num); });
            $orden = array_unique(array_column($numRes,'ord'));
            $pregunta = array_unique(array_column($numRes,'pregunta'));

            $pregunta = array(
                'orden' =>  $orden[0],
                'pregunta' =>  $pregunta[0],
                'opciones' => array()
            );

            $p = array_push($parsed['preguntas'],$pregunta);

            foreach($options as $o){

                $optRes = array_filter($numRes, function($ar) use($o){ return ($ar['opcion'] == $o); });
                $total = array();

                foreach ($optRes as $per){
                    $total = $per['suma'];
                }

                $opcion = array(
                    'opcion' => $o,
                    'total' => $total
                );

                array_push($parsed['preguntas'][$p-1]['opciones'],$opcion);
            }
        }

        return $parsed;
    }
}