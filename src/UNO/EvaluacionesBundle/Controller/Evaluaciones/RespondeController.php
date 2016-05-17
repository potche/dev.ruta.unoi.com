<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 3/10/15
 * Time: 12:33
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\DateTime;
use UNO\EvaluacionesBundle\Entity\Log;


class RespondeController extends Controller
{

    /**
     * @Route("/responde/{id}")
     *
     */
    public function indexAction(Request $request, $id){
        $surveyID = $id;
        $session = $request->getSession();
        /*
         * This section checks if the user is already logged in,
         * in that case $personId gets the personIdS value from the session variable
         * otherwise is redirected to the Login
         * */
        if ( !$session->has('logged_in') ) {
            return $this->redirectToRoute('login');
        }else{
            if(!Utils::isSurveyAuthorized($session,$surveyID)){
                return $this->render('@UNOEvaluaciones/Evaluaciones/responderError.html.twig',array(
                    'title'=>'Error',
                    'message'=>'Lo sentimos, el contenido que buscas es erróneo',
                ));
            }else{
                $_survey = $this->getQuestions($surveyID);
                $surveyJson = $this->creaJson($_survey);

                return new Response($surveyJson);
            }
        }
    }

    private function getQuestions($surveyId){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_survey = $qb->select("S.surveyid, S.title, S.description, Q.questionid, QS.order, Q.question, Sub.subcategoryid, Sub.subcategory, OQ.optionxquestionId, O.optionid, O.option")
            ->from('UNOEvaluacionesBundle:Survey','S')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'S.surveyid = QS.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'QS.questionxsurveyId = OQ.questionxsurvey')
            ->innerJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'OQ.optionOptionid = O.optionid')
            ->where('S.surveyid = :surveyId')
            ->setParameter('surveyId', $surveyId)
            ->orderBy('Q.questionid, QS.order, O.optionid')
            ->getQuery()
            ->getResult();

        return $_survey;
    }

    private function creaJson($_survey){
        $surveyJson = array(
            'surveyid' => $_survey[0]['surveyid'],
            'title' => $_survey[0]['title'],
            'description' => $_survey[0]['description']
        );

        $question = array();

        foreach(array_unique(array_column($_survey,'questionid','order')) as $valQue){
            $i = 0;
            foreach($_survey as $valSur){
                if($valQue == $valSur['questionid']){
                    $options['questionid'] = $valSur['questionid'];
                    $options['order'] = $valSur['order'];
                    $options['question'] = $valSur['question'];
                    $options['subcategoryid'] = $valSur['subcategoryid'];
                    $options['subcategory'] = $valSur['subcategory'];

                    $options['options'][$i] = array(
                        "optionid" => $valSur['optionid'],
                        "option" => $valSur['option'],
                        "optionxquestionId" => $valSur['optionxquestionId']
                    );
                    $i++;
                }
            }
            array_push($question,$options);
        }
        $surveyJson['questions'] = $question ;
        print_r($surveyJson);
        return json_encode($surveyJson);
    }


}