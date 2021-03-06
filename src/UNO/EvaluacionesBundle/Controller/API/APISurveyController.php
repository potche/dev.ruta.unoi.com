<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * @Route("/api/v0/survey")
 *
 */
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
        $response->setData($this->getByPerson($personid));

        return $response;
    }

    protected function getBySurvey($surveyid = null) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $condition = $surveyid != null ? 'su.surveyid = '.$surveyid : 'su.surveyid > 1';

        $all = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, l.schoollevel as titulonivel, p.profileid as perfil, p.profile as tituloperfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','p','WITH','p.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','l','WITH','l.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sc','WITH','sc.subcategoryid = q.subcategorySubcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->where($condition)
            ->groupBy('id, titulo, nivel,titulonivel, perfil, tituloperfil, pregunta, categoria, oxq.optionOptionid')
            ->getQuery()
            ->getResult();

        return $all ? $this->buildArray($all): APIUtils::getErrorResponse('404');
    }

    protected function getByPersonSchool($schoolid = null, $schoollevelid = null, $profileid = null){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $condition = "su.surveyid > 1";

        //Se agregan las condiciones segun sea el caso
        $condition = $schoolid != null ? $condition.' AND ps.schoolid = '.$schoolid : $condition;
        $condition = $schoollevelid != null ? $condition.' AND sxp.schoollevelid = '.$schoollevelid : $condition;
        $condition = $profileid != null ? $condition.' AND sxp.profileProfileid = '.$profileid : $condition;

        $bySchool = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, lvl.schoollevel as titulonivel, ps.profileid as perfil, p.profile as tituloperfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','p','WITH','p.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','lvl','WITH','lvl.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sc','WITH','sc.subcategoryid = q.subcategorySubcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->where($condition)
            ->groupBy('id, titulo, nivel, titulonivel, perfil, tituloperfil, pregunta, categoria, oxq.optionOptionid')
            ->getQuery()
            ->getResult();

        return $bySchool ? $this->buildArray($bySchool): APIUtils::getErrorResponse('404');
    }

    private function getByPerson($personid){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $byPerson = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, sxp.schoollevelid as nivel, lvl.schoollevel as titulonivel, ps.profileid as perfil, p.profile as tituloperfil, qxs.order as numpregunta ,q.question as pregunta, sc.subcategory as categoria, oxq.order as numopcion, oxq.optionxquestionId, o.option as opcion, COALESCE(a.actioncode,'0') AS actioncode")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','p','WITH','p.profileid = sxp.profileProfileid')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','lvl','WITH','lvl.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','qxs','WITH','qxs.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','q','WITH','q.questionid = qxs.questionQuestionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','sc','WITH','sc.subcategoryid = q.subcategorySubcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','oxq','WITH','oxq.questionxsurvey = qxs.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Option','o','WITH','o.optionid = oxq.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.surveySurveyid = su.surveyid AND l.personPersonid = :personId')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','l.actionaction = a.idaction')
            ->where('ps.personid = :personId')
            ->groupBy('id, titulo, nivel, titulonivel, perfil, tituloperfil, pregunta, categoria, oxq.optionOptionid, a.actioncode')
            ->setParameter('personId',$personid)
            ->getQuery()
            ->getResult();

        return $byPerson ? $this->buildArray($byPerson) : APIUtils::getErrorResponse('404');
    }

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

                $surveys[$s['id']]['roles'][$s['nivel']] = array(
                    'Nivel' => $s['titulonivel'],
                    'Perfiles' => array(),
                );
            }

            if(!isset($surveys[$s['id']]['roles'][$s['nivel']]['Perfiles'][$s['perfil']])){

                $surveys[$s['id']]['roles'][$s['nivel']]['Perfiles'][$s['perfil']] = array(
                    'perfil' => $s['perfil'],
                    'titulo' => $s['tituloperfil']
                );
            }


            /*if(!in_array($s['perfil'],$surveys[$s['id']]['roles'][$s['nivel']])) {

                array_push($surveys[$s['id']]['roles'][$s['nivel']],$s['perfil']);
            }*/



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

    /**
     * @Route("/editQuestion")
     * @Method({"POST"})
     * actualiza el texto de la preguna de un instrumento
     */
    public function editQuestionAction(Request $request){

        $questionId= $request->request->get('questionId');
        $question= $request->request->get('question');

        $em = $this->getDoctrine()->getManager();
        $Question = $em->getRepository('UNOEvaluacionesBundle:Question')->findOneBy(array('questionid' => $questionId));
        if ($Question) {
            $Question->setQuestion($question);
            $em->flush();
            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }
    }

    /**
     * @Route("/deleteQuestion")
     * @Method({"POST"})
     * bora una la preguna de un instrumento
     */
    public function deleteQuestionAction(Request $request){

        $questionId= $request->request->get('questionId');
        $surveyId= $request->request->get('surveyId');

        $em = $this->getDoctrine()->getManager();
        $Question = $em->getRepository('UNOEvaluacionesBundle:Questionxsurvey')->findOneBy(
            array(
                'questionQuestionid' => $questionId,
                'surveySurveyid' => $surveyId)
        );

        if ($Question) {
            $order = $Question->getOrder();
            $em->remove($Question);
            $em->flush();

            $this->executeQuery(
                "UPDATE QuestionXSurvey QS
                    SET QS.order = QS.order-1
                    WHERE
                        QS.Survey_surveyId = $surveyId
                            AND QS.order > $order
            ");

            return new JsonResponse(array('status' => true),200);
        }else{
            return new JsonResponse(array('status' => false),400);
        }

    }

    private function executeQuery($query){
        $em = $this->getDoctrine()->getManager();

        $em->getConnection()->beginTransaction();

        try {
            $statement = $em->getConnection()->prepare($query);
            $statement->execute();
            $em->getConnection()->commit();

            return true;

        }catch (\PDOException $e) {
            return false;
        }


    }
}