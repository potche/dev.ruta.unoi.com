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
     * @Route("/survey/school/{schoolId}",
     * requirements={"schoolId" = "\d+"},
     * defaults={"schoolId" = null})
     * @Method({"GET"})
     */
    public function surveySchoolAction($schoolId){

        $result = $this->getResQuerySurvey(array('schoolId' => $schoolId), 'PS.schoolid = :schoolId');

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
            ->andWhere('S.closingdate >= CURRENT_DATE()')
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
     * @Route("/programs")
     */
    public function programAction(){

        $result = $this->getResQueryProgram();

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * obtiene una lista con los programas
     * la almacena en el atributo $this->_program
     */
    private function getResQueryProgram() {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("CP.programId, CP.nameProgram")
            ->from('UNOEvaluacionesBundle:Cprogram','CP')
            ->getQuery()
            ->getResult();

    }

    /**
     * @Route("/grades/{schoolLevelId}")
     */
    public function gradeAction($schoolLevelId){

        $result = $this->getResQueryGrade($schoolLevelId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * obtiene una lista con los grados
     */
    private function getResQueryGrade($schoolLevelId) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("CG.gradeId, CG.nameGrade")
            ->from('UNOEvaluacionesBundle:Cgrade','CG')
            ->where("CG.schoolLevelId in ($schoolLevelId)")
            ->orderBy('CG.schoolLevelId')
            ->getQuery()
            ->getResult();

    }

    /**
     * @Route("/groups")
     */
    public function groupAction(){

        $result = $this->getResQueryGroup();

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * obtiene una lista con los grupos
     */
    private function getResQueryGroup() {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $groupIdArray = array();

        $_groupId = $qb->select("CG.groupId")
            ->from('UNOEvaluacionesBundle:Cgroup','CG')
            ->getQuery()
            ->getResult();

        foreach($_groupId as $value){
            array_push($groupIdArray, $value['groupId']);
        }

        return $groupIdArray;
    }

    /**
     * @Route("/personAssigned/{personId}")
     */
    public function personAssignedAction($personId){

        $result = $this->getResQuerypersonAssigned($personId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * Valida si el usuario tiene asignado grupo y grado
     */
    private function getResQueryPersonAssigned($personId) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        return $qb->select("PA.personAssignedId, SL.schoollevel, CG.nameGrade, Cg.groupId, CP.nameProgram")
            ->from('UNOEvaluacionesBundle:Personassigned','PA')
            ->innerjoin('UNOEvaluacionesBundle:Schoollevel','SL','WITH', 'PA.schoolLevelId = SL.schoollevelid')
            ->innerjoin('UNOEvaluacionesBundle:Cgrade','CG','WITH', 'PA.gradeId = CG.gradeId')
            ->innerjoin('UNOEvaluacionesBundle:Cgroup','Cg','WITH', 'PA.groupId = Cg.groupId')
            ->innerjoin('UNOEvaluacionesBundle:Cprogram','CP','WITH', 'PA.programId = CP.programId')
            ->where('PA.personId = :personId')
            ->setParameter('personId', $personId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/personProfile/{personId}")
     */
    public function personProfileAction($personId){

        $result = $this->getResQuerypersonProfile($personId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @return string
     * obtiene el Id del schoolLevel(kinder, primaria, secundaria) si es profesor
     */
    private function getResQuerypersonProfile($personId){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        return $qb->select('PS.schoollevelid, SL.schoollevel, CASE WHEN (PA.personAssignedId IS NULL) THEN false ELSE true END as Ok ')
            ->from('UNOEvaluacionesBundle:Personschool', 'PS')
            ->innerJoin('UNOEvaluacionesBundle:Schoollevel','SL','WITH', 'PS.schoollevelid = SL.schoollevelid')
            ->leftJoin('UNOEvaluacionesBundle:Personassigned','PA','WITH', 'PS.personid = PA.personId AND PS.schoollevelid = PA.schoolLevelId')
            ->where('PS.personid = :personId')
            ->andWhere('PS.profileid = 18')
            ->setParameter('personId', $personId)
            ->groupBy('PS.schoollevelid')
            ->getQuery()
            ->getResult();
    }
}