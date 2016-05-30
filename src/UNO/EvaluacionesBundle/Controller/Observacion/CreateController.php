<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 23/05/16
 * Time: 12:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Observacion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CreateController
 * @package UNO\EvaluacionesBundle\Controller\Observacion
 */
class CreateController extends Controller{

    /**
     * @Route("/observacion/crear/{observationId}")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function indexAction(Request $request, $observationId){
        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseURL = $this->container->get('router')->getContext()->getBaseUrl();
        $baseUrl = "http://dev.ruta.unoi.com".$this->container->get('router')->getContext()->getBaseUrl();

        //$schoolListAPI = json_decode(file_get_contents("$baseUrl/api/v0/catalog/schools", false), true);

        $session = $request->getSession();
        $session->start();
        if($this->valObservationIdByCoach($observationId, $session->get('personIdS'))){
            $result = $this->getResQueryOQ($observationId);

            return $this->render('UNOEvaluacionesBundle:Observacion:create.html.twig', array(
                'questionByCategory' => $result,
                'observationId' => $observationId
            ));
        }else{
            throw new AccessDeniedHttpException('No estás autorizado para ver este contenido');
        }
    }

    /**
     * @param $parameters
     * @param $where
     * @return mixed
     */
    private function getResQueryOQ($observationId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $obQ = $qb->select("Q.questionid, QS.order, Q.question, Sub.subcategoryid, Sub.subcategory, OA.observationAnswerId, OA.answer, OA.comment, OA.personId, OA.dateRecord")
            ->from('UNOEvaluacionesBundle:Observation', 'O')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS','WITH','QS.surveySurveyid = O.surveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q','WITH','QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub','WITH','Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->leftJoin('UNOEvaluacionesBundle:ObservationAnswer','OA','WITH','OA.questionId = Q.questionid AND O.observationId = OA.observationId')
            ->andWhere('O.observationId = :observationId')
            ->setParameter('observationId', $observationId)
            ->orderBy( 'QS.order')
            ->getQuery()
            ->getResult();

        $questionByCategory = array();
        $subcategorys = array_unique(array_column($obQ, 'subcategory','subcategoryid'));

        foreach ($subcategorys as $key => $subcategory){
            $questions = array();
            foreach ($obQ as $question){
                if($question['subcategory'] === $subcategory){
                    array_push($questions, array(
                        'order' => $question['order'],
                        'question' => $question['question'],
                        'questionId' => $question['questionid'],
                        'observationAnswerId' => $question['observationAnswerId'],
                        'answer' => gettype($question['answer']) == 'boolean' ? (int)$question['answer'] : $question['answer'],
                        'comment' => $question['comment'],
                        'personId' => $question['personId'],
                        'dateRecord' => $question['dateRecord']
                    ));
                }
            }
            array_push($questionByCategory, array('categoryId' => $key, 'category' => $subcategory, 'questions' => $questions));
        }

        return $questionByCategory;
    }

    private function valObservationIdByCoach($observationId, $coachId){
        $em = $this->getDoctrine()->getManager();
        $observationAssigned = $em->getRepository('UNOEvaluacionesBundle:Observation')->findOneBy(array('observationId' => $observationId, 'coachId' => $coachId));
        if ($observationAssigned) {
            #si el coach asignada la observacion
            $status = true;
        }else{
            $status = false;
        }
        return $status;
    }

}
