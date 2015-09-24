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

class ListarController extends Controller
{
    /**
     * Landing endpoint que devuelve la vista con las evaluaciones de un usuario
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
        if (!$session->has('personIdS') && !$session->has('nameS')) {

            return $this->redirectToRoute('login');
        }

        // Tomamos el id del usuario para poder hacer las consultas necesarias
        $personID = $session->get('personIdS');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        // Seleccionamos los perfiles del usuario, para posteriormente traer las evaluaciones asignadas a éstos
        $profs = $qb->select('pr.profileid')
            ->from('UNOEvaluacionesBundle:Profile', 'pr')
            ->innerJoin('UNOEvaluacionesBundle:Personschool', 'ps', 'WITH', 'ps.profileid = pr.profileid')
            ->innerJoin('UNOEvaluacionesBundle:Person', 'pe', 'WITH', 'pe.personid = ps.personid')
            ->where('pe.personid = :personID')
            ->setParameter('personID', $personID)
            ->getQuery()
            ->getResult();

        // Agregamos los ids de los perfiles a una cadena para pasarlos como rango a la siguiente consulta
        $profiles = '';
        foreach($profs as $r) {
            $profiles .= $r['profileid'].', ';
        }
        $profiles = rtrim($profiles,', ');

        // Tomamos todas las evaluaciones activas que le corresponden al usuario de acuerdo a sus perfiles
        if($profiles) {

            $qb = $em->createQueryBuilder();
            $evals = $qb->select('su.surveyid, su.title, su.closingdate')
                ->from('UNOEvaluacionesBundle:Survey', 'su')
                ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile', 'sxp', 'WITH', 'sxp.surveySurveyid = su.surveyid')
                ->add('where', $qb->expr()->in('sxp.profileProfileid',$profiles))
                ->andWhere('su.active = 1')
                ->groupBy('su.surveyid, su.title, su.closingdate')
                ->getQuery()
                ->getResult();
        }

        // Para efectos estadísticos llevamos la cuenta de cuántas evaluaciones faltan por responder
        $countToBeAnswered = 0;
        $surveyList = array();

        /*
         * De cada encuesta preguntamos si ya está resuelta,
         * o está pendiende de resolver. Para ello utilizamos
         * estos códigos:
         *
         * 4: El usuario ya respondió la evaluación
         * 5: El usuario dejó incompleta la evaluación
         *
         */
        $qb = $em->createQueryBuilder();
        foreach ($evals as $survey) {

            $query = $qb->select('l')
            ->from('UNOEvaluacionesBundle:Log', 'l')
            ->where('l.personPersonid = :personId')
            ->andWhere('l.surveySurveyid = :surveyId')
            ->andWhere('l.actionaction IN (4,5)')
            ->orderBy('l.date','DESC')
            ->setParameters(array(
                'personId' => $personID,
                'surveyId' => $survey['surveyid'],
            ))
            ->getQuery()
            ->getResult();

            if(empty($query)) {

                $status = 0;
                $countToBeAnswered++;
            }
            else {

                $status = $query[0]->getActionaction()->getIdaction();
                $status == 5 ? $countToBeAnswered++ : null;
            }

            // Agregamos al arreglo de encuestas lo necesario para mostrarlas en el dashboard
            array_push($surveyList,array(
                'id' => $survey['surveyid'],
                'title' => $survey['title'],
                'closingDate' => $survey['closingdate']->format('j/M/Y \@ g:i a'),
                'status' => $status,
            ));
        }

        // Generamos las estadísticas que necesitamos agregar al dashboard
        $statistics = $this->fetchStats(count($evals),$countToBeAnswered);

        return $this->render('@UNOEvaluaciones/Evaluaciones/listar.html.twig', array(

            'surveyList' => $surveyList,
            'stats' => $statistics,
        ));
    }

    /**
     * Método para generar estadísticas de las evaluaciones del usuario.
     *
     * Devuelve un arreglo con:
     *
     * answered: el número de evaluaciones completadas (total - encuestasASerRespondidas)
     * toBeAnswered: el número de evaluaciones a ser completadas
     * compliance: el porcentaje de cumplimiento del usuario (número de encuestas respondidas * 100 / total)
     *
     * @param $countSurveys
     * @param $countToBeAnswered
     * @return array
     *
     * @author jbravob julio@dsindigo.com
     */
    private function fetchStats($countSurveys, $countToBeAnswered) {

        $answeredCount = $countSurveys - $countToBeAnswered;
        $compliancePercentage = ($countSurveys > 0 ? (($answeredCount * 100)/$countSurveys): 0);

        return array(
            'answered' => $answeredCount,
            'toBeAnswered' => $countToBeAnswered,
            'compliance' => $compliancePercentage,
        );
    }
}