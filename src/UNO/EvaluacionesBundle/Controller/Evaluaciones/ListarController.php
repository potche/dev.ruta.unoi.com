<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:18
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListarController extends Controller
{

    /**
     * Controlador que maneja la lista de evaluaciones respondidas y por responder del usuario
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     *
     * @version 0.1.0
     * @author jbravob julio@dsindigo.com
     * @copyright Sistema UNO Internacional. 2015
     */
    public function indexAction(Request $request) {

        $session = $request->getSession();
        // Si no se tiene iniciada una sesión, se redirige al login
        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }
        $content = $this->get("request")->getContent();

        $personID = $session->get('personIdS');
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
            ->setParameter('personId',$personID)
            ->getQuery()
            ->getResult();

        $countToBeAnswered = array_count_values(array_column($surveys,'actioncode'))['0'];
        $session->set('authorized_in',base64_encode(json_encode(array_column($surveys,'surveyid'))));

        $statistics = Utils::fetchStats(count($surveys),$countToBeAnswered);
        $session->set('compliance',$statistics['compliance']);

        return $this->render('@UNOEvaluaciones/Evaluaciones/listar.html.twig', array(

            'surveyList' => $surveys,
            'stats' => $statistics,
        ));
    }
}