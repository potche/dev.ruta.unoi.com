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

class InicioController extends Controller{

    private $_personId;
    private $_profile = array();

    public function indexAction(Request $request) {

        $session = $request->getSession();
        $session->start();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirect("/");
        }

        $this->_personId = $session->get('personIdS');
        $this->setProfile($session->get('profileS'));

        $global = null;
        $colegios = null;
        $colegio = null;
        $personas = null;
        $coaches = null;
        $personal = json_decode(file_get_contents($this->generateUrl('APIStatsProgressByPerson',array('personid' => $this->_personId),true),false),true);

        if(in_array('SuperAdmin',$this->_profile)){

            $global = json_decode(file_get_contents($this->generateUrl('APIStatsProgress',array(),true),false),true)['global']['Stats'][0]["y"];
            $colegios = $this->getColegiosCount();
            $personas = $this->getPersonsCount();
            $coaches = $this->getCoachesCount();
            $personal = null;
        }

        if (in_array('Director',$this->_profile)){

            $colegio = json_decode(file_get_contents($this->generateUrl('APIStatsProgressBySchool',array('schoolid' => $this->getSchoolid($this->_personId)),true),false),true)['global']['Stats'][0]["y"];
        }

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:inicio.html.twig', array(
            'global' => $global,
            'colegios'=> $colegios,
            'colegio' => $colegio,
            'personas'=> $personas,
            'coaches' => $coaches,
            'personal' => $personal
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

        $coachesCount = $qb->select("COUNT(DISTINCT(p.personid)) as cuenta")
            ->from('UNOEvaluacionesBundle:Person','p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.personid = p.personid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr','WITH','pr.profileid = ps.profileid')
            ->where(" pr.profilecode = :profile ")
            ->groupBy('p.personid')
            ->setParameter('profile','COACH')
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

}