<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 09/09/16
 * Time: 12:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Interaction;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Class CreateController
 * @package UNO\EvaluacionesBundle\Controller\Interaction
 */
class CreateController extends Controller{

    /**
     * @Route("/interaction/crear/{interactionId}")
     *
     * obtiene y crea la tabla con el detalle de la evaluacion
     */
    public function indexAction(Request $request, $interactionId){
        
        $session = $request->getSession();
        $session->start();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'inicio',
                'with' => 'none'
            ));
        }

        if($this->valInteractionIdByCoach($interactionId, $session->get('personIdS')) == 200){
            $result = $this->getResQueryOQ($interactionId);

            return $this->render('UNOEvaluacionesBundle:Interaction:create.html.twig', array(
                'questionByCategory' => $result,
                'interactionId' => $interactionId
            ));
        }else{
            throw new AccessDeniedHttpException('No estás autorizado para ver este contenido');
        }
    }

    /**
     * @param $interactionId
     * @return mixed
     */
    private function getResQueryOQ($interactionId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $obQ = $qb->select("Q.questionid, QS.order, Q.question, Sub.subcategoryid, Sub.subcategory, OA.interactionAnswerId, OA.answer, OA.comment, OA.personId, OA.dateRecord")
            ->from('UNOEvaluacionesBundle:Interaction', 'O')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS','WITH','QS.surveySurveyid = 22')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q','WITH','QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub','WITH','Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->leftJoin('UNOEvaluacionesBundle:InteractionAnswer','OA','WITH','OA.questionId = Q.questionid AND O.interactionId = OA.interactionId')
            ->where('O.interactionId = :interactionId')
            ->setParameter('interactionId', $interactionId)
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
                        'interactionAnswerId' => $question['interactionAnswerId'],
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

    private function valInteractionIdByCoach($interactionId, $coachId){
        $em = $this->getDoctrine()->getManager();
        $interactionAssigned = $em->getRepository('UNOEvaluacionesBundle:Interaction')->findOneBy(array('interactionId' => $interactionId, 'coachId' => $coachId));
        if ($interactionAssigned) {
            if($interactionAssigned->getFinish()){
                #ya esta finalizada por lo cual no la puede editar
                $status = 403;
            }else {
                #si el coach asignada la observacion
                $status = 200;
            }
        }else{
            #no le corresponde o no existe
            $status = 404;
        }
        return $status;
    }

}
