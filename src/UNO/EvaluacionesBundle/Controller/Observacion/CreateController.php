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
            $result = $this->getResQueryOQ(null, null);

            return $this->render('UNOEvaluacionesBundle:Observacion:create.html.twig', array(
                'questionByCategory' => $result
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
    private function getResQueryOQ($parameters = null, $where = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $obQ = $qb->select("Q.questionid, QS.order, Q.question, Sub.subcategoryid, Sub.subcategory")
            ->from('UNOEvaluacionesBundle:Questionxsurvey','QS')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q','WITH','QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub','WITH','Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->andWhere('QS.surveySurveyid = 19')
            //->setParameters($parameters)
            ->orderBy( 'QS.order')
            ->getQuery()
            ->getResult();

        $questionByCategory = array();
        $subcategorys = array_unique(array_column($obQ, 'subcategory','subcategoryid'));

        foreach ($subcategorys as $key => $subcategory){
            $questions = array();
            foreach ($obQ as $question){
                if($question['subcategory'] === $subcategory){
                    array_push($questions, array('order' => $question['order'], 'question' => $question['question'], 'questionId' => $question['questionid']));
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
