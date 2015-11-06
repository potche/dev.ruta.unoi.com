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
        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        // Tomamos el id del usuario para poder hacer las consultas necesarias
        $personID = $session->get('personIdS');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        // Seleccionamos los perfiles del usuario, para posteriormente traer las evaluaciones asignadas a éstos
        $profs = $qb->select('ps.profileid', 'ps.schoollevelid')
            ->from('UNOEvaluacionesBundle:Personschool', 'ps')
            ->where('ps.personid = :personID')
            ->groupBy('ps.profileid, ps.schoollevelid')
            ->setParameter('personID', $personID)
            ->getQuery()
            ->getResult();

        // Agregamos los ids de los perfiles y niveles del usuario a una cadena para pasarlos como rango a la siguiente consulta

        $profiles = implode(',',array_unique(array_column($profs,'profileid')));
        $levels = implode(',',array_unique(array_column($profs,'schoollevelid')));

        // Tomamos todas las evaluaciones activas que le corresponden al usuario de acuerdo a sus perfiles
        $evals = null;

        if($profiles && $levels) {

            $qb = $em->createQueryBuilder();
            $evals = $qb->select('su.surveyid, su.title, su.closingdate, sxp.schoollevelid')
                ->from('UNOEvaluacionesBundle:Survey', 'su')
                ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile', 'sxp', 'WITH', 'sxp.surveySurveyid = su.surveyid')
                ->add('where', $qb->expr()->andx(
                    $qb->expr()->in('sxp.profileProfileid',$profiles),
                    $qb->expr()->orx(
                        $qb->expr()->in('sxp.schoollevelid',$levels),
                        $qb->expr()->eq('sxp.schoollevelid','0')
                    )
                ))
                ->andWhere('su.active = 1')
                ->andWhere('su.closingdate >= CURRENT_DATE()')
                ->groupBy('su.surveyid, su.title, su.closingdate')
                ->orderBy('su.title')
                ->getQuery()
                ->getResult();
        }

        // Para efectos estadísticos llevamos la cuenta de cuántas evaluaciones faltan por responder
        $countToBeAnswered = 0;
        $surveyList = array();

        // Consulta para hallar qué evaluaciones tienen un estatus de terminadas, se compararán con las primeras obtenidas

        $qb = $em->createQueryBuilder();
        $answered = $qb->select('su.surveyid')
            ->from('UNOEvaluacionesBundle:Survey', 'su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile', 'sxp', 'WITH', 'sxp.surveySurveyid = su.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Log', 'l', 'WITH','l.surveySurveyid = su.surveyid')
            ->add('where', $qb->expr()->andx(
                $qb->expr()->in('sxp.profileProfileid',$profiles),
                $qb->expr()->in('sxp.schoollevelid',$levels)
            ))
            ->andWhere('su.active = 1')
            ->andWhere('l.personPersonid = :personId')
            ->andWhere('l.actionaction = 4')
            ->groupBy('su.surveyid')
            ->setParameters(array(
                'personId' => $personID
            ))
            ->getQuery()
            ->getResult();

        $answered_surveys = array_column($answered,'surveyid');

        //Asigno estatus de evaluaciones con base en la consulta previa

        if($evals != null){

            foreach ($evals as $survey) {

                if(!in_array($survey['surveyid'],$answered_surveys)) {

                    $status = 5;
                    $countToBeAnswered++;

                } else {

                    $status = 4;
                }

                // Agregamos al arreglo de encuestas lo necesario para mostrarlas en el dashboard
                array_push($surveyList,array(
                    'id' => $survey['surveyid'],
                    'title' => $survey['title'],
                    'closingDate' => $survey['closingdate']->format('j/M/Y \@ g:i a'),
                    'status' => $status,
                ));
            }
            // Tengo que agregar como variable de sesión las encuestas a las que el usuario tiene derecho responder
            $session->set('authorized_in',base64_encode(json_encode(array_column($evals,'surveyid'))));
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
        $compliancePercentage = ($countSurveys > 0 ? number_format((($answeredCount * 100)/$countSurveys), 2, '.', ''): 0);

        return array(
            'answered' => $answeredCount,
            'toBeAnswered' => $countToBeAnswered,
            'compliance' => $compliancePercentage,
        );
    }
}