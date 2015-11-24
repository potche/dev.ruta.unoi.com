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
        if ($session->get('logged_in')) {
            $this->_personId = $session->get('personIdS');
            $this->setProfile($session->get('profileS'));

            if (array_intersect(array('SuperAdmin'), $this->_profile)) {
                $asignadas = $this->getAsignadasGrl();
                $realizadas = $this->getRealizadasGrl();
                $titleAvance = 'Porcentaje Global de Todas las Evaluaciones.';
            }else{
                $asignadas = $this->getAsignadasUser();
                $realizadas = $this->getRealizadasUser();
                $titleAvance = 'Porcentaje Global de mis Evaluaciones.';
            }

            if ($asignadas != 0) {
                $avance = $this->getPorcentaje($asignadas, $realizadas, $redondear = 1);
            } else {
                $avance = 0;
            }

            return $this->render('UNOEvaluacionesBundle:Evaluaciones:inicio.html.twig', array(
                'avance' => $avance,
                'titleAvance' => $titleAvance
            ));
        }else{
            return $this->redirect("/");
        }
    }

    private function getRealizadasUser(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $surveys = $qb->select("su.surveyid, su.title, su.closingdate, COALESCE(a.actioncode,'0') AS actioncode")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid AND ps.personid = :personId')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.surveySurveyid = su.surveyid AND l.personPersonid = :personId')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','l.actionaction = a.idaction')
            ->where('su.active = 1')
            ->andWhere('su.closingdate >= CURRENT_DATE()')
            ->groupBy('su.surveyid, su.title, su.closingdate, a.actioncode')
            ->setParameter('personId',$this->_personId)
            ->getQuery()
            ->getResult();

            return !array_key_exists('004', array_count_values(array_column($surveys,'actioncode'))) ? 0 : array_count_values(array_column($surveys,'actioncode'))['004'];

    }

    private function getAsignadasUser(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

		$surveys = $qb->select("su.surveyid, su.title, su.closingdate, COALESCE(a.actioncode,'0') AS actioncode")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid AND ps.personid = :personId')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.surveySurveyid = su.surveyid AND l.personPersonid = :personId')
            ->leftJoin('UNOEvaluacionesBundle:Action','a','WITH','l.actionaction = a.idaction')
            ->where('su.active = 1')
            ->andWhere('su.closingdate >= CURRENT_DATE()')
            ->groupBy('su.surveyid, su.title, su.closingdate, a.actioncode')
            ->setParameter('personId',$this->_personId)
            ->getQuery()
            ->getResult();

            return count($surveys);
    }

    private function getRealizadasGrl(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $answeredBySurvey= $qb->select("su.surveyid, count(distinct ps.personid) as answeredNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log','log','WITH','ps.personid = log.personPersonid AND sxp.surveySurveyid = log.surveySurveyid')
            ->where('log.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->andWhere('log.actionaction = 4')
            ->groupBy('sxp.surveySurveyid')
            ->getQuery()
            ->getResult();

        $realizadas = 0;
        foreach($answeredBySurvey as $value){
            $realizadas += (int)$value['answeredNum'];
        }
        return $realizadas;
    }

    private function getAsignadasGrl(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $expectedBySurvey = $qb->select("su.surveyid, count(distinct p.personid) as expectedNum")
            ->from('UNOEvaluacionesBundle:Person', 'p')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','p.personid = ps.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp', 'WITH','sxp.profileProfileid = ps.profileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','su','WITH','su.surveyid = sxp.surveySurveyid')
            ->where('sxp.surveySurveyid > 1')
            ->andWhere('p.admin NOT IN (1)')
            ->groupBy('sxp.surveySurveyid')
            ->getQuery()
            ->getResult();

        //print_r($expectedBySurvey);
        $asignadas = 0;
        foreach($expectedBySurvey as $value){
            $asignadas += (int)$value['expectedNum'];
        }

        return $asignadas;
    }

    /**
     * @param $total
     * @param $parte
     * @param int $redondear
     * @return float
     *
     * Calcula el porcentaje
     */
    private function getPorcentaje($total, $parte, $redondear = 2) {
        return round(($parte*100) / $total, $redondear);
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