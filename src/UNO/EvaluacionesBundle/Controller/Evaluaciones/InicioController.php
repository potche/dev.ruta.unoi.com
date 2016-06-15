<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 13/10/15
 * Time: 12:18
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

class InicioController extends Controller{

    private $_personId;
    private $_profile = array();

    public function indexAction(Request $request) {

        $session = $request->getSession();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'inicio',
                'with' => 'none'
            ));
        }

        $this->_personId = $session->get('personIdS');
        $this->setProfile($session->get('profileS'));

        $colegios = null;
        $colegio = null;
        $personas = null;
        $coaches = null;
        $observationsByCoach = null;
        $personal = json_decode(file_get_contents($this->generateUrl('APIStatsProgressByPerson',array('personid' => $this->_personId),true),false),true);

        if(in_array('SuperAdmin',$this->_profile)){
            $colegios = $this->getColegiosCount();
            $personas = $this->getPersonsCount();
            $coaches = $this->getCoachesCount();
            $personal = null;
        }

        if(in_array('COACH',$this->_profile)){
            $observationsByCoach = $this->observationsByCoachPV();
        }

        if (in_array('Director',$this->_profile)){
            $colegio = json_decode(file_get_contents($this->generateUrl('APIStatsProgressBySchool',array('schoolid' => $this->getSchoolid($this->_personId)),true),false),true)['global']['Stats'][0]["y"];
        }

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:inicio.html.twig', array(
            'colegios'=> $colegios,
            'colegio' => $colegio,
            'personas'=> $personas,
            'coaches' => $coaches,
            'personal' => $personal,
            'observationsByCoach' => $observationsByCoach
        ));
    }

    private function getColegiosCount(){

        return count($this->getDoctrine()->getRepository('UNOEvaluacionesBundle:School')->findAll());
    }

    private function getPersonsCount(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $personsLog = $qb->select("p.personid as persona, COUNT(l.logid) as logcuenta")
            ->from('UNOEvaluacionesBundle:Person','p')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','p.personid = l.personPersonid')
            ->groupBy('persona')
            ->getQuery()
            ->getResult();

        return array(
            'pending' => count(array_filter($personsLog, function($ar) { return ($ar['logcuenta'] == 0); })),
            'answered' => count(array_filter($personsLog, function($ar) { return ($ar['logcuenta'] != 0); })),
            'total' => count($personsLog)
        );
    }

    private function getCoachesCount(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $coachesCount = $qb->select("COUNT(DISTINCT(ps.personid)) as cuenta")
            ->from('UNOEvaluacionesBundle:Personschool','ps')
            ->where(" ps.profileid = :profile ")
            ->setParameter('profile',2)
            ->getQuery()
            ->getResult();

        return $coachesCount;
    }

    private function getSchoolid($personId){

        return $this->getDoctrine()->getRepository('UNOEvaluacionesBundle:Personschool')->findOneBy(array(
            'personid' => $personId
        ))->getSchoolid();
    }

    /**
     * @param $profileS
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setProfile($profileS){
        $profileJson = json_decode($profileS);

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    private function observationsByCoachPV(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $oByC = $qb->select("O.observationId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS name, S.school, O.gradeId, O.groupId, Cp.nameProgram, O.start, O.finish")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','O.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','O.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','O.programId = Cp.programId')
            ->where('O.coachId = :coachId')
            ->andWhere('O.finish is null')
            ->setParameter('coachId', $this->_personId)
            ->orderBy( 'O.observationId')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        $nivel = array('K' => 'Kinder', 'P' => 'Primaria', 'S' => 'Secundaria', 'B' => 'Bachillerato');
        $observationsByCoach = array();
        foreach ($oByC as $row){
            $row['nivel'] = strtoupper($row['gradeId'][1]);
            $row['nivelCompleto'] = $nivel[strtoupper($row['gradeId'][1])];
            $row['grado'] = $row['gradeId'][0].'°';
            array_push($observationsByCoach, $row);
        }
        return $observationsByCoach;
    }

}