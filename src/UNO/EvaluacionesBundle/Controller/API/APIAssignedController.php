<?php

namespace UNO\EvaluacionesBundle\Controller\API;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Entity\Cgroup;
use UNO\EvaluacionesBundle\Entity\Personassigned;
use UNO\EvaluacionesBundle\Entity\Person;

use UNO\EvaluacionesBundle\Controller\API\APIUtils;

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 8/12/15
 * Time: 3:05 PM
 */

define('SCHOOLLEVELID','schoolLevelId');
define('GRADEID','gradeId');
define('GROUPID','groupId');
define('PROGRAMID','programId');
define('PERSONID','personId');
define('STATUS','status');
define('MESSAGE','message');
/**
 * @Route("/api/v0/assigned")
 *
 */
class APIAssignedController extends Controller{
private $_request;

    /**
     * @Route("/add")
     * @Method({"POST"})
     */
    public function AddAction(Request $request){
        $this->_request = $request;

        $post = array(
            SCHOOLLEVELID => (int)$request->request->get(SCHOOLLEVELID),
            GRADEID => $request->request->get(GRADEID),
            GROUPID => $request->request->get(GROUPID),
            PROGRAMID => (int)$request->request->get(PROGRAMID),
            PERSONID => (int)$request->request->get(PERSONID)
        );

        $result = $this->getResQueryAdd($post);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * agrega un grupo, grado, nivel al profesor
     */
    private function getResQueryAdd($post) {

        $personAssigned = new Personassigned();

        $personAssigned->setSchoolLevelId($post[SCHOOLLEVELID]);
        $personAssigned->setGradeId($post[GRADEID]);
        $personAssigned->setGroupId($post[GROUPID]);
        $personAssigned->setProgramId($post[PROGRAMID]);
        $personAssigned->setPersonId($post[PERSONID]);


        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($personAssigned);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
        $session = $this->_request->getSession();
        $session->start();
        $session->set('assignedS', 1);

        return array(
            STATUS => 'ok',
            MESSAGE => 'Saved new personAssigned with id '.$personAssigned->getPersonAssignedId()
        );
    }

    /**
     * @Route("/remove")
     * @Method({"POST"})
     */
    public function RemoveAction(Request $request){
        $personAssignedId = (int)$request->request->get('personAssignedId');


        $result = $this->getResQueryRemove($personAssignedId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * elimina la asinacion por grupo, grado, nivel al profesor
     */
    private function getResQueryRemove($personAssignedId) {

        $em = $this->getDoctrine()->getManager();
        $personAssigned = $em->getRepository('UNOEvaluacionesBundle:Personassigned')->findOneBy(array('personAssignedId' => $personAssignedId));
        if ($personAssigned) {
            $em->remove($personAssigned);
            // actually executes the queries (i.e. the DELETE query)
            $em->flush();
        }

        return array(
            STATUS => 'ok',
            MESSAGE => 'Delete row'
        );
    }

    /**
     * @Route("/personById/{personId}")
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
            ->setParameter(PERSONID, $personId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/byId/{assignedId}")
     */
    public function byIdAction($assignedId){

        $result = $this->getResQueryById($assignedId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * busca una clase asignada por id
     */
    private function getResQueryById($assignedId) {
        $assigned = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Personassigned')
            ->findOneBy(array('personAssignedId' => $assignedId));

        if ($assigned) {
            return array(
                'gradeId' => $assigned->getGradeId(),
                'groupId' => $assigned->getGroupId(),
                'programId' => $assigned->getProgramId()
            );
        }
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

        return $qb->select("CG.gradeId, CG.nameGrade, CG.schoolLevelId")
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
            array_push($groupIdArray, $value[GROUPID]);
        }

        return $groupIdArray;
    }

    /**
     * @Route("/personProfile/{personId}")
     *
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
            ->setParameter(PERSONID, $personId)
            ->groupBy('PS.schoollevelid')
            ->getQuery()
            ->getResult();
    }

    /**
     * @Route("/addGroup")
     * @Method({"POST"})
     */
    public function AddGroupAction(Request $request){
        $groupId = $request->request->get(GROUPID);

        $result = $this->getResQueryAddGroup($groupId);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * agrega un grupo, grado, nivel al profesor
     */
    private function getResQueryAddGroup($groupId) {

        $group = new Cgroup();

        $group->setGroupId($groupId);

        $em = $this->getDoctrine()->getManager();
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($group);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return array(
            STATUS => 'ok',
            MESSAGE => 'Saved new personAssigned with id '.$group->getGroupId()
        );
    }

    /**
     * @Route("/ok")
     * @Method({"POST"})
     */
    public function OkAction(Request $request){
        $personId = $request->request->get(PERSONID);

        $result = $this->activeInactive($personId,1);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    /**
     * @Route("/inactive")
     * @Method({"POST"})
     */
    public function inactiveAction(Request $request){
        $personId = $request->request->get(PERSONID);

        $result = $this->activeInactive($personId,0);

        #-----envia la respuesta en JSON-----#
        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

    private function activeInactive($personId,$assigned){
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('UNOEvaluacionesBundle:Person')->findOneBy(
            array('personid' => $personId)
        );

        if (!$person) {
            $m = array(
                STATUS => 'error',
                MESSAGE => 'No person found for id '.$personId
            );
        }else{
            $person->setAssigned($assigned);
            $em->flush();

            $m = array(
                STATUS => 'ok',
                MESSAGE => 'update personAssigned'
            );
        }

        return $m;
    }
}