<?php
/**
 * Created by PhpStorm.
 * User: jbravob
 * Date: 16/12/15
 * Time: 1:44 PM
 */

namespace UNO\EvaluacionesBundle\Controller\API;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Class APINotificationsController
 * @package UNO\EvaluacionesBundle\Controller\API
 * @author jbravo
 *
 * En esta clase se manejan diversas llamadas a consultas que entregan información necesaria para la construcción de mensajes
 * de notificación para ser empleados en la misma aplicación o por medio de mail.
 *
 */
class APINotificationsController extends Controller
{

    /**
     *
     * Búsqueda de evaluaciones nuevas dado el número de días antes de hoy
     *
     * @param Request $request
     * @param $daysago "Dias atrás para buscar notificaciones"
     * @return JsonResponse "Respuesta en formato JSON"
     * @throws \Exception
     */
    public function newsurveysAction(Request $request, $daysago){

        $response = new JsonResponse();
        $response->setData($this->getNewSurveys($daysago));
        return $response;
    }

    /**
     *
     * Obtención de Top 5 de cumplimiento de usuarios dado un id de colegio
     *
     * @param Request $request
     * @param $schoolid
     * @return JsonResponse
     * @throws \Exception
     */

    public function toppersonsbyscholAction(Request $request, $schoolid){

        $response = new JsonResponse();
        $response->setData($this->getTop5BySchool($schoolid));
        return $response;
    }

    /**
     *
     * Obtención de las personas que tienen progreso incompleto de evaluaciones
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function generalpendingAction(Request $request){

        $response = new JsonResponse();
        $response->setData($this->getGeneralPending());
        return $response;
    }

    /**
     *
     * Obtención de directores para el resúmen semanal
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function principalsAction(Request $request){

        $response = new JsonResponse();
        $response->setData($this->getPrincipals());
        return $response;
    }

    /**
     *
     * Método interno para consultar evaluaciones nuevas dado cierto número de días antes de hoy
     *
     * @param $daysago
     * @return array "Arreglo con respuesta en formato JSON"
     */

    protected function getNewSurveys($daysago){

        if($daysago > 7 && $daysago != 0){

            return APIUtils::getErrorResponse('400');
        }

        $date1 = date('Y-m-d',mktime(date("H"), date("i"), date("s"), date("m"), date("d")-$daysago, date("Y")));

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $all = $qb->select("su.surveyid as id, su.title as titulo, su.active as activa, su.creationdate as creada, su.closingdate as fechacierre, su.createdby as creadapor, p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre")
            ->from('UNOEvaluacionesBundle:Survey','su')
            ->innerJoin('UNOEvaluacionesBundle:Surveyxprofile','sxp','WITH','su.surveyid = sxp.surveySurveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->where($qb->expr()->between('su.creationdate',':daysago','CURRENT_TIMESTAMP()'))
            ->andWhere('p.mailing = 1')
            ->andWhere('p.active = 1')
            ->andWhere('su.active = 1')
            ->groupBy('id, persona')
            ->setParameter('daysago',$date1)
            ->getQuery()
            ->getResult();

        return $all ? $this->parseArray($all) : APIUtils::getErrorResponse('404');
    }

    /**
     *
     * Método interno para consultar top de cumnplimiento de usuarios de acuerdo un colegio dado
     *
     * @param $schoolid
     * @return array "Arreglo con respuesta en formato JSON"
     */

    protected function getTop5BySchool($schoolid){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $all = $qb->select("p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre, COUNT(DISTINCT(l.surveySurveyid)) as respondidas, COUNT(DISTINCT (sxp.surveySurveyid)) AS esperadas")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.personPersonid = p.personid AND sxp.surveySurveyid = l.surveySurveyid')
            ->where('ps.schoolid = :schoolId')
            ->groupBy('p.personid')
            ->add('orderBy', 'esperadas DESC')
            ->setParameter('schoolId',$schoolid)
            ->getQuery()
            ->getResult();

        return $all ? $this->parseForTop($all) : APIUtils::getErrorResponse('404');
    }

    /**
     * Obtiene los usuarios con progreso pendiente que estén activos en el sistema y que tengan habilitadas las notificaciones
     * @return array
     */

    protected function getGeneralPending(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        /*$all = $qb->select("p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre, COUNT(DISTINCT(l.surveySurveyid)) as respondidas, COUNT(DISTINCT (sxp.surveySurveyid)) AS esperadas, (COUNT(DISTINCT(l.surveySurveyid))/COUNT(DISTINCT (sxp.surveySurveyid))*100) AS perc")
            ->from('UNOEvaluacionesBundle:Surveyxprofile','sxp')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.profileid = sxp.profileProfileid AND ps.schoollevelid = sxp.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid')
            ->leftJoin('UNOEvaluacionesBundle:Log','l','WITH','l.personPersonid = p.personid AND sxp.surveySurveyid = l.surveySurveyid')
            ->groupBy('p.personid')
            ->where('p.mailing = 1 AND p.active = 1')
            ->add('having', 'perc < 100')
            ->add('orderBy', 'perc')
            ->getQuery()
            ->getResult();*/

        $all = array();

        array_push($all,array(
            'persona' => 3882669,
            'email' => 'bovarbj90@gmail.com',
            'nombre' => 'Julio Bravo',
            'respondidas' => 3,
            'esperadas' => 9,
            'perc' => 33.3333

        ));

        return $all ? $this->removeInvalidItems($all) : APIUtils::getErrorResponse('404');
    }

    /**
     *
     * Obtener directores activos y con notificaciones activas de todos los colegios registrados.
     * @return mixed
     */

    protected function getPrincipals(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        /*$principals = $qb->select("p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre, s.schoolid as idescuela, s.school as escuela")
            ->from('UNOEvaluacionesBundle:School','s')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.schoolid = s.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr','WITH','ps.profileid = pr.profileid AND pr.profilecode = :profile')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid AND p.mailing = 1')
            ->where('p.mailing = 1 AND p.active = 1')
            ->setParameter('profile', 'DIR')
            ->groupBy('p.personid')
            ->getQuery()
            ->getResult();*/

        $principals = array();

        array_push($principals,array(
            'persona' => 3882669,
            'email' => 'bovarbj90@gmail.com',
            'nombre' => 'Julio Bravo',
            'idescuela' => 3902,
            'escuela' => 'SOLUCIONES DIGITALES UNO'
        ));

        return $principals ? $this->removeInvalidItems($principals): APIUtils::getErrorResponse('404');
    }

    /**
     * Método interno auxiliar para parsear un arreglo que contiene las personas y sus nuevas evaluaciones
     *
     * @param $all
     * @return array "Arreglo con campos Persona: int, Nombre: string, Email: string, NewSurveys: array"
     */

    private function parseArray($all){

        $parsedArray = array();
        $users = array_unique(array_column($all,'persona'));

        foreach ($users as $u) {

            $byperson = array_filter($all, function($ar) use($u){ return ($ar['persona'] == $u); });
            array_push($parsedArray,array(
                'Persona'=> array_column($byperson,'persona')[0],
                'Nombre' => array_column($byperson,'nombre')[0],
                'Email' => array_column($byperson,'email')[0],
                'NewSurveys' => $byperson
            ));
        }
        return $parsedArray;
    }

    /**
     *
     * Tratamiento para los usuarios que han respondido o no todas sus evaluaciones
     *
     * @param $all
     * @return array
     */

    private function parseForTop($all){

        $top = array();
        $pending = array();

        foreach ($all as $a){

            $avance = $a['esperadas'] > 0 ? round(($a['respondidas'] / $a['esperadas']) * 100, 2) : 0;
            $p = array(
                'persona' => $a['persona'],
                'email' => $a['email'],
                'nombre' => $a['nombre'],
                'progreso' => $a['respondidas'].'/'.$a['esperadas'],
                'avance' => $avance
            );

            if($p['avance'] < 100.00){

                array_push($pending,$p);
            }
            else if ($p['avance'] == 100.00){

                array_push($top,$p);
            }
        }

        usort($pending, function ($a,$b){

            if ($a['avance'] == $b['avance']) {

                return 0;
            }

            return ($a['avance'] > $b['avance']) ? -1 : 1;
        });

        return array('pendiente'=>$pending,'terminado'=>$top);
    }

    /**
     *
     * Función para remover de la lista a usuarios que no tienen correo electrónico
     *
     * @param $items
     * @return mixed
     */

    private function removeInvalidItems($items){

        foreach($items as $k => $item){

            if(!Utils::isMailRegexValid($item['email'])){

                unset($item[$k]);
            }
        }

        return $items;
    }
}