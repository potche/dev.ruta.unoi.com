<?php
/**
 * Created by PhpStorm.
 * User: Isra
 * Date: 08/11/16
 * Time: 17:23 PM
 */

namespace UNO\EvaluacionesBundle\Controller\API_SimuladorCostos;


class APIUtils
{
    public function jsonResponse(array $data, $code, $massage = null){

        return array(
            'results' => $data,
            'status' => $this->getHTTPStatusResponse($code, $massage)
        );
        
    }

    private function getHTTPStatusResponse($code, $massage = null){

        if(!$massage){

            switch ($code) {
                case '200':
                    $massage = 'ok';
                    break;
                case '204':
                    $massage = 'La petición se ha completado con éxito pero su respuesta no tiene ningún contenido';
                    break;
                case '404':
                    $massage = 'Resource not exists';
                    break;
                case '403':
                    $massage = 'Not authorized, please login';
                    break;
                case '400':
                    $massage = 'Bad request, please build a valid one';
                    break;
                case '405':
                    $massage = 'Method Not Allowed';
                    break;
                default:
                    $massage = 'Unknown error, try again later';
                    break;
            }

        }

        return array(
            'code' => $code,
            'message' => $massage
        );
    }

}