<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use UNO\EvaluacionesBundle\Controller\API\APIUtils;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 8/12/15
 * Time: 3:05 PM
 */

/**
 * @Route("/api/v0/catalog")
 *
 */
class APICatalogoController extends Controller{

    /**
     * @Route("/surveys")
     *
     */
    public function surveysAction(){

        $result = $this->getResQuerySurvey(array('schoolId' => ''), 'PS.schoolid <> :schoolId');

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/survey/school/{schoolId}/level",
     * requirements={"schoolId" = "\d+"},
     * defaults={"schoolId" = null})
     * @Method({"POST"})
     */
    public function surveySchoolAction(Request $request, $schoolId){
        $schoolLevelId = $request->request->get('schoolLevelId');

        $result = $this->getResQuerySurvey(array('schoolId' => $schoolId), 'PS.schoolid = :schoolId AND PS.schoollevelid in ('.$schoolLevelId.')');

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @param $parameters
     * @param $where
     * @return mixed
     */
    private function getResQuerySurvey($parameters, $where){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_surveyList = $qb->select("S.surveyid, S.title")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile ','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'S.surveyid = SP.surveySurveyid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            //->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere($where)
            ->setParameters($parameters)
            ->groupBy('S.surveyid')
            ->orderBy( 'S.surveyid')
            ->getQuery()
            ->getResult();

        return($_surveyList);
    }

    /**
     * @Route("/schools")
     */
    public function schoolAction(){

        $result = $this->getResQuerySchool();

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * obtiene una lista con loas escuelas para poder filtrar (unicamente para Admin)
     * la almacena en el atributo $this->_schoolId
     */
    private function getResQuerySchool() {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_schoolId = $qb->select("trim(PS.schoolid) as schoolid, trim(S.school) as school")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:School','S', 'WITH', 'PS.schoolid = S.schoolid')
            ->groupBy('PS.schoolid')
            ->orderBy('PS.schoolid')
            ->getQuery()
            ->getResult();

        return $_schoolId;
    }

    /**
     * @Route("/personSchool/{schoolId}")
     */
    public function personSchoolAction($schoolId){

        $result = $this->getResQueryPersonSchool($schoolId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * obtiene una lista con loas escuelas para poder filtrar (unicamente para Admin)
     * la almacena en el atributo $this->_schoolId
     */
    private function getResQueryPersonSchool($schoolId) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_personSchool = $qb->select("P.personid, P.user, concat(trim(P.name),' ',trim(P.surname)) as nombre, P.email")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->where('PS.schoolid = :schoolId')
            ->setParameter('schoolId', $schoolId)
            ->groupBy('PS.personid','PS.schoolid')
            ->orderBy('PS.personid')
            ->getQuery()
            ->getResult();


        return $_personSchool;
    }

    /**
     * @Route("/validUniqueMail/{email}")
     * busca que el mail que tiene el usuario no lo tenga otro usuario
     */
    public function validUniqueMailAction($email) {
        $em = $this->getDoctrine()->getManager();
        $Person = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(array('email' => $email));
        if ($Person) {
            #si existe el mail, por lo que hay q pedirle q ingrece otro
            $status = array("status" => 0);
        }else{
            $status = array("status" => 1);
        }

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($status);
        return $response;
    }

    /**
     * @Route("/usersBySchool/{schoolId}")
     */
    public function usersBySchoolAction($schoolId){
        $result = $this->getQuerySPS(
            "P.personid, P.user, concat(trim(P.name),' ',trim(P.surname)) as nombre, P.email",
            'PS.schoolid = :schoolId ',
            array('schoolId' => $schoolId),
            'P.personid'
        );

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/surveysBySchool/{schoolId}")
     */
    public function surveyBySchoolAction($schoolId){
        $result = $this->getQuerySPS(
            "S.surveyid, S.title",
            'PS.schoolid = :schoolId ',
            array('schoolId' => $schoolId),
            'S.surveyid'
        );

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/personBy/survey/{surveyId}/school/{schoolId}")
     */
    public function personBySurveySchoolAction($surveyId,$schoolId){

        $result = $this->getQuerySPS(
            "P.personid, P.user, concat(trim(P.name),' ',trim(P.surname)) as nombre, P.email",
            'PS.schoolid = :schoolId and S.surveyid = :surveyId',
            array('schoolId' => $schoolId, 'surveyId' => $surveyId),
            'PS.personid','S.surveyid'
        );

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function getQuerySPS($select, $where, $parameters, $groupBy){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select($select)
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'SP.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:School','Sc', 'WITH', 'Sc.schoolid = PS.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:Log','L', 'WITH', 'P.personid = L.personPersonid AND S.surveyid = L.surveySurveyid')
            ->where( $where )
            ->setParameters( $parameters )
            ->groupBy($groupBy)
            ->orderBy('S.surveyid')
            ->getQuery()
            ->getResult();

    }

    /**
     * @Route("/questionSurvey/{surveyId}")
     */
    public function questionSurveyAction($surveyId){


        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $rs = $qb->select("QS.order, Q.questionid, Q.question")
            ->from('UNOEvaluacionesBundle:Survey','S')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'S.surveyid = QS.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'QS.questionxsurveyId = OQ.questionxsurvey')
            ->where( 'S.surveyid = :surveyId' )
            ->setParameters( array('surveyId' => $surveyId) )
            ->groupBy('QS.order')
            ->getQuery()
            ->getResult();

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($rs);

        return $response;
    }
}