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

define('ANSWERID', 'answerId');
define('ANSWERIDM', 'answerid');
define('ANSWER', 'answer');
define('COMMENT','comment');
define('PERSONPERSONID','personPersonId');
define('OPTIONXQUESTIONID','optionXquestionId');
define('DATEHISTORY','dateHistory');
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
            ->setParameter(ANSWERID, $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/addRes")
     * @Method({"POST"})
     */
    public function AddResAction(Request $request){
        $answerId = (int)$request->request->get(ANSWERID);
        $answerHistory = $this->findAnswer($answerId);

        $this->updateAnswer($answerId,$request->request->get(ANSWER), null);

        $result = $this->getResQueryAdd($answerHistory);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/addComment")
     * @Method({"POST"})
     */
    public function AddCommentAction(Request $request){
        $answerId = (int)$request->request->get(ANSWERID);
        $answerHistory = $this->findAnswer($answerId);

        $this->updateAnswer($answerId, null, $request->request->get(COMMENT));

        $result = $this->getResQueryAdd($answerHistory);

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
        $answerHistory = $qb->select('A.answerid, A.answer, A.comment, P.personid, OQ.optionxquestionId')
            ->from('UNOEvaluacionesBundle:Answer', 'A')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH', 'A.personPersonid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ','WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->where('A.answerid = :answerId')
            ->setParameter(ANSWERID, $id)
            ->getQuery()
            ->getResult();

        return array(
            ANSWERID => (int)$answerHistory[0][ANSWERIDM],
            ANSWER => $answerHistory[0][ANSWER],
            COMMENT => $answerHistory[0][COMMENT],
            PERSONPERSONID => (int)$answerHistory[0]['personid'],
            OPTIONXQUESTIONID => (int)$answerHistory[0]['optionxquestionId'],
            DATEHISTORY => new \DateTime()
        );
    }

    private function updateAnswer($id, $answer = null, $comment = null){
        $em = $this->getDoctrine()->getManager();
        $Answer = $em->getRepository('UNOEvaluacionesBundle:Answer')->findOneBy(
            array(ANSWERIDM => $id)
        );

        if ($Answer) {
            if($answer) {
                $Answer->setAnswer($answer);
            }

            if($comment) {
                $Answer->setComment($comment);
            }

            $em->flush();
        }
    }

    /**
     * agrega un grupo, grado, nivel al profesor
     */
    private function getResQueryAdd($post) {

        $AnswerHistory = new Answerhistory();

        $AnswerHistory->setAnswerId($post[ANSWERID]);
        $AnswerHistory->setAnswer($post[ANSWER]);
        $AnswerHistory->setComment($post[COMMENT]);
        $AnswerHistory->setPersonPersonId($post[PERSONPERSONID]);
        $AnswerHistory->setOptionXquestionId($post[OPTIONXQUESTIONID]);
        $AnswerHistory->setDateHistory($post[DATEHISTORY]);

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