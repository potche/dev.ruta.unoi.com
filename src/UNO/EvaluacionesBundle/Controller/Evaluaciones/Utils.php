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
class Utils
{

    /**
     * Devuelve si una evaluacion dada es visible al usuario que la solicita.
     *
     * @param $session
     * @param $surveyId
     * @return bool
     */

    public static function isSurveyAuthorized($session, $surveyId)
    {

        $availableSurveys = json_decode(base64_decode($session->get('authorized_in')));

        if ($availableSurveys == null || !in_array($surveyId, $availableSurveys)) {

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

    public static function isUserAdmin($session)
    {

        $profiles = json_decode($session->get('profileS'));

        foreach ($profiles as $p) {

            if ($p->profileid == 1) {

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

    public static function isUserLoggedIn($session)
    {

        $loggedIn = $session->get('logged_in');

        if (!$loggedIn) {

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

    public static function fetchStats($countSurveys, $countToBeAnswered)
    {

        $answeredCount = $countSurveys - $countToBeAnswered;
        $compliancePercentage = ($countSurveys > 0 ? number_format((($answeredCount * 100) / $countSurveys), 2, '.', '') : 0);

        return array(
            'answered' => $answeredCount,
            'toBeAnswered' => $countToBeAnswered,
            'compliance' => $compliancePercentage,
        );
    }

    public static function isMailRegexValid($mailString)
    {

        return filter_var($mailString, FILTER_VALIDATE_EMAIL);
    }

    public static function http_response_code($code = NULL)
    {

        if ($code !== NULL) {

            switch ($code) {
                case 100:
                    $text = 'Continue';
                    break;
                case 101:
                    $text = 'Switching Protocols';
                    break;
                case 200:
                    $text = 'OK';
                    break;
                case 201:
                    $text = 'Created';
                    break;
                case 202:
                    $text = 'Accepted';
                    break;
                case 203:
                    $text = 'Non-Authoritative Information';
                    break;
                case 204:
                    $text = 'No Content';
                    break;
                case 205:
                    $text = 'Reset Content';
                    break;
                case 206:
                    $text = 'Partial Content';
                    break;
                case 300:
                    $text = 'Multiple Choices';
                    break;
                case 301:
                    $text = 'Moved Permanently';
                    break;
                case 302:
                    $text = 'Moved Temporarily';
                    break;
                case 303:
                    $text = 'See Other';
                    break;
                case 304:
                    $text = 'Not Modified';
                    break;
                case 305:
                    $text = 'Use Proxy';
                    break;
                case 400:
                    $text = 'Bad Request';
                    break;
                case 401:
                    $text = 'Unauthorized';
                    break;
                case 402:
                    $text = 'Payment Required';
                    break;
                case 403:
                    $text = 'Forbidden';
                    break;
                case 404:
                    $text = 'Not Found';
                    break;
                case 405:
                    $text = 'Method Not Allowed';
                    break;
                case 406:
                    $text = 'Not Acceptable';
                    break;
                case 407:
                    $text = 'Proxy Authentication Required';
                    break;
                case 408:
                    $text = 'Request Time-out';
                    break;
                case 409:
                    $text = 'Conflict';
                    break;
                case 410:
                    $text = 'Gone';
                    break;
                case 411:
                    $text = 'Length Required';
                    break;
                case 412:
                    $text = 'Precondition Failed';
                    break;
                case 413:
                    $text = 'Request Entity Too Large';
                    break;
                case 414:
                    $text = 'Request-URI Too Large';
                    break;
                case 415:
                    $text = 'Unsupported Media Type';
                    break;
                case 500:
                    $text = 'Internal Server Error';
                    break;
                case 501:
                    $text = 'Not Implemented';
                    break;
                case 502:
                    $text = 'Bad Gateway';
                    break;
                case 503:
                    $text = 'Service Unavailable';
                    break;
                case 504:
                    $text = 'Gateway Time-out';
                    break;
                case 505:
                    $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }
        } else {
            $text = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $text;
    }
}