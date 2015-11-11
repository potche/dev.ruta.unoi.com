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

        $realizadas = $qb->select("P.personid", "CONCAT(P.name, ' ', P.surname) AS username", "COUNT(DISTINCT (S.title)) AS realizadas")
            ->from('UNOEvaluacionesBundle:Person','P')
            ->innerJoin('UNOEvaluacionesBundle:Answer','A', 'WITH', 'P.personid = A.personPersonid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'A.personPersonid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','SP', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->where('P.personid = :personId')
            ->setParameter('personId', $this->_personId)
            ->getQuery()
            ->getResult();

        return $realizadas[0]['realizadas'];
    }

    private function getAsignadasUser(){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $asignadas = $qb->select("P.personid", "CONCAT(P.name, ' ', P.surname) AS username", "COUNT(DISTINCT (SP.surveySurveyid)) AS asignadas")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','SP')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->where('P.personid = :personId')
            ->setParameter('personId', $this->_personId)
            ->getQuery()
            ->getResult();

        return $asignadas[0]['asignadas'];
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
            ->andWhere('log.actionaction = 5')
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
        return round($parte / $total * 100, $redondear);
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