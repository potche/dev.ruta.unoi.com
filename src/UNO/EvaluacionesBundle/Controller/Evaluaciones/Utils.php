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
class Utils {

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
}