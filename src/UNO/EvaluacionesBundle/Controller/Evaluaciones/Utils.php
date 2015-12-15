<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 12/10/15
 * Time: 10:38 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;

/**
 *
 * Clase que contiene diversos métodos comunes en los controladores del módulo de evaluaciones
*/
class Utils{

    /**
     * Devuelve si una evaluacion dada es visible al usuario que la solicita.
     *
     * @param $session
     * @param $surveyId
     * @return bool
     */

    public static function isSurveyAuthorized($session, $surveyId) {

        $availableSurveys = json_decode(base64_decode($session->get('authorized_in')));

        if($availableSurveys==null || !in_array($surveyId, $availableSurveys)) {

            return false;
        }
        return true;
    }

    /**
     *
     * Valida que un usuario sea super-administrador del sistema
     *
     * @param $session
     * @return bool
     * @author julio
     * @version 0.2.0
     */

    public static function isUserAdmin($session) {

        $profiles = json_decode($session->get('profileS'));

        foreach ($profiles as $p) {

            if($p->profileid == 1){

                return true;
            }
        }

        return false;
    }

    /**
     *
     * Valida que el usuario esté logueado y que tenga una sesión activa
     *
     * @param $session
     * @return bool
     * @author julio
     * @version 0.2.0
     *
     */

    public static function isUserLoggedIn($session) {

        $loggedIn = $session->get('logged_in');

        if(!$loggedIn){

            return false;
        }
        return true;
    }

    /**
     *
     * Devuelve el porcentaje de avance dados el total de evaluaciones y el total de las pendientes
     *
     * @param $countSurveys
     * @param $countToBeAnswered
     * @return array
     *
     */

    public static function fetchStats($countSurveys, $countToBeAnswered) {

        $answeredCount = $countSurveys - $countToBeAnswered;
        $compliancePercentage = ($countSurveys > 0 ? number_format((($answeredCount * 100)/$countSurveys), 2, '.', ''): 0);

        return array(
            'answered' => $answeredCount,
            'toBeAnswered' => $countToBeAnswered,
            'compliance' => $compliancePercentage,
        );
    }
}