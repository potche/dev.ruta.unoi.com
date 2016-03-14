<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 08/12/15
 * Time: 11:11 AM
 */

namespace UNO\EvaluacionesBundle\Controller\API;


class APIUtils
{
    public static function getErrorResponse($code){

        switch($code){

            case '404':
                $reason = 'Resource not exists';
                break;
            case '403':
                $reason = 'Not authorized, please login';
                break;
            case '400':
                $reason = 'Bad request, please build a valid one';
                break;
            default:
                $reason = 'Unknown error, try again later';
                break;
        }

        return array(
            'Error' => $reason,
            'Code' => $code
        );
    }

}