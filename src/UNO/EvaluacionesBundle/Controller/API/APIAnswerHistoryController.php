<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Answerhistory;

use UNO\EvaluacionesBundle\Controller\API\APIUtils;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 27/04/16
 * Time: 1:25 PM
 */

/**
 * @Route("/api/v0/answerHistory")
 *
 */
class APIAnswerHistoryController extends Controller{

    /**
     * @Route("/byId/{id}")
     * @Method({"GET"})
     */
    public function byIdAction($id){

        $result = $this->getResQueryById($id);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * get answer history by id
     */
    private function getResQueryById($id) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        return $qb->select('A.answerId, A.answer, A.comment, A.personPersonId, A.optionXquestionId, A.dateHistory')
            ->from('UNOEvaluacionesBundle:Answerhistory', 'A')
            ->where('A.answerId = :answerId')
            ->setParameter('answerId', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/add")
     * @Method({"POST"})
     */
    public function AddAction(Request $request){

        $answerHistory = $this->findAnswer((int)$request->request->get('answerId'));

        $post = array(
            "answerId" => (int)$answerHistory[0]['answerid'],
            "answer" => $answerHistory[0]['answer'],
            "comment" => $answerHistory[0]['comment'],
            "personPersonId" => (int)$answerHistory[0]['personid'],
            "optionXquestionId" => (int)$answerHistory[0]['optionxquestionId'],
            "dateHistory" => new \DateTime()
        );

        $this->updateAnswer((int)$request->request->get('answerId'),$request->request->get('answer'), $request->request->get('comment'));

        $result = $this->getResQueryAdd($post);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * get answer history by id
     */
    private function findAnswer($id) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        return $qb->select('A.answerid, A.answer, A.comment, P.personid, OQ.optionxquestionId')
            ->from('UNOEvaluacionesBundle:Answer', 'A')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH', 'A.personPersonid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ','WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->where('A.answerid = :answerId')
            ->setParameter('answerId', $id)
            ->getQuery()
            ->getResult();
    }

    private function updateAnswer($id, $answer, $comment){
        $em = $this->getDoctrine()->getManager();
        $Answer = $em->getRepository('UNOEvaluacionesBundle:Answer')->findOneBy(
            array('answerid' => $id)
        );

        if ($Answer) {
            $Answer->setAnswer($answer);
            $Answer->setComment($comment);
            $em->flush();
        }
    }

    /**
     * agrega un grupo, grado, nivel al profesor
     */
    private function getResQueryAdd($post) {

        $AnswerHistory = new Answerhistory();

        $AnswerHistory->setAnswerId($post['answerId']);
        $AnswerHistory->setAnswer($post['answer']);
        $AnswerHistory->setComment($post['comment']);
        $AnswerHistory->setPersonPersonId($post['personPersonId']);
        $AnswerHistory->setOptionXquestionId($post['optionXquestionId']);
        $AnswerHistory->setDateHistory($post['dateHistory']);

        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($AnswerHistory);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return array(
            'status' => 'ok',
            'message' => 'Saved new Answer History'
        );
    }

}