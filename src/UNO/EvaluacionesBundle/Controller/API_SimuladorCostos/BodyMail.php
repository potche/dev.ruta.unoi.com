<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 17/09/15
 * Time: 10:28 AM
 * Class Login
 * @package UNO\EvaluacionesBundle\Controller\Login
 */

namespace UNO\EvaluacionesBundle\Controller\API_SimuladorCostos;

class BodyMail{

    public function run($nombre, array $parameters) {
        $aviso = "Este correo electrónico, así como los archivos adjuntos que contenga, es confidencial de conformidad con las leyes aplicables, y es para uso exclusivo del destinatario al que expresamente se le ha enviado. Si usted no es el destinatario legítimo del mismo, deberá reportarlo al remitente del correo y borrarlo inmediatamente. Cualquier revisión, retransmisión, divulgación, difusión o cualquier otro uso de este correo, por personas o entidades distintas a las del destinatario legítimo, queda expresamente prohibido. Los derechos de propiedad respecto de la información, material, documentos electrónicos, bases de datos, diseños, y los distintos elementos en él contenidos, son titularidad de alguna de las empresas de Grupo Santillana en México. Este correo electrónico, no pretende ni debe ser considerado como constitutivo de ninguna relación legal, contractual o de otra índole similar.";

        return '<html xmlns="http://www.w3.org/1999/xhtml">

                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <meta name="viewport" content="width=device-width">
                    <style type="text/css">
                        @media only screen and (max-width: 550px),
                        screen and (max-device-width: 550px) {
                            body[yahoo] .buttonwrapper {
                                background-color: transparent !important;
                            }
                            body[yahoo] .button {
                                padding: 0 !important;
                            }
                            body[yahoo] .button a {
                                background-color: #9b59b6;
                                padding: 15px 25px !important;
                            }
                        }

                        @media only screen and (min-device-width: 601px) {
                            .content {
                                width: 600px !important;
                            }
                            .col387 {
                                width: 387px !important;
                            }
                        }
                    </style>
                </head>

                <body style="margin: 0; padding: 0; background-color:#ededed;" yahoo="fix">
                    <!--[if (gte mso 9)|(IE)]>
                        <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                          <tr>
                            <td>
                        <![endif]-->
                    <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 600px;" class="content">
                        <tbody>
                            <tr>
                                <td style="padding: 15px 10px 15px 10px;">

                                </td>
                            </tr>

                            <tr>
                                <td align="center" bgcolor="#59AAC2" style="padding: 20px 20px 20px 20px; color: #ffffff; font-family: Arial, sans-serif; font-size: 36px; font-weight: bold;">
                                    <img src="https://ruta.unoi.com/public/assets/images/login/logoUnoHome.png" alt="Launch Icon" style="width: 25%;display:block;">
                                    <br/>
                                    <strong>Propuesta</strong>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 40px 20px 40px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px; border-bottom: 1px solid #f6f6f6; text-align: justify;">
                                    Estimad@ <strong>' . $nombre . '</strong>
                                    <p>Acontinuación se muestra la Propuesta realizada a travez del Simulador de Costos</p>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-a.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Aula Digital</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['aulaDigital'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-b.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Maker Cart</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['makerCart'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-c.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Aula Meker</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['aulaMaker'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-d.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Proyector Interactivo</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['proyector'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-e.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Telepresencia</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['telepresencia'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-f.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Aceleración de Contenido </td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['aceleracon'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-g.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Certificación ISO</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['certificacion'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#ffffff" style="padding: 10px 20px 5px 20px; color: #555555; font-family: Arial, sans-serif; font-size: 15px; line-height: 24px;">

                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="width: 10%;">
                                                    <img src="https://ruta.unoi.com/public/assets/images/simulador/logo-h.png">
                                                </td>
                                                <td align="left" style="width: 50%;padding: 20px 0px 20px 20px;">Desarrollo Profesional Apple</td>
                                                <td align="right" style="width: 40%;padding: 20px 20px 20px 20px;"><strong>'. $parameters['desarrollo'] .'</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#f9f9f9" style="padding: 10px; font-family: Arial, sans-serif; border-bottom: 1px solid #f6f6f6;">
                                    <table style="width: 100%; color: #59AAC2;" border="0" cellspacing="0" cellpadding="0" class="buttonwrapper">
                                        <thead>
                                            <tr>
                                                <th align="right" style="width: 70%; padding: 5px;">Saldo en Puntos</th>
                                                <th align="right" style="width: 30%; padding-right: 30px;">'. $parameters['puntosSaldo'] .'</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#f9f9f9" style="padding: 10px; font-family: Arial, sans-serif; border-bottom: 1px solid #f6f6f6;">
                                    <table style="width: 100%; color: #59AAC2;" border="0" cellspacing="0" cellpadding="0" class="buttonwrapper">
                                        <thead>
                                            <tr>
                                                <th align="right" style="width: 70%; padding: 5px;">Saldo en Pesos</th>
                                                <th align="right" style="width: 30%; padding-right: 30px;">'. $parameters['totalPesos'] .'</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#f9f9f9" style="padding: 10px; font-family: Arial, sans-serif; border-bottom: 1px solid #f6f6f6;">
                                    <table style="width: 100%; color: #59AAC2;" border="0" cellspacing="0" cellpadding="0" class="buttonwrapper">
                                        <thead>
                                            <tr>
                                                <th align="right" style="width: 70%; padding: 5px;">Extención de Contrato (años)</th>
                                                <th align="right" style="width: 30%; padding-right: 30px;">'. $parameters['totalAños'] .'</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td bgcolor="#f9f9f9" style="padding: 10px; font-family: Arial, sans-serif; border-bottom: 1px solid #f6f6f6;">
                                    <table style="width: 100%; color: #59AAC2;" border="0" cellspacing="0" cellpadding="0" class="buttonwrapper">
                                        <thead>
                                            <tr>
                                                <th align="right" style="width: 70%; padding: 5px;">% de Particiáción</th>
                                                <th align="right" style="width: 30%; padding-right: 30px;">'. $parameters['porcentajeParticipacion'] .'</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" bgcolor="#dddddd" style="padding: 15px 10px 15px 10px; color: #555555; font-family: Arial, sans-serif; font-size: 12px; line-height: 18px;">
                                    <b>UNOi ® </b><br>
                                    <p>Aviso Legal</p>
                                    <p class="aviso" style="text-align: justify;">
                                        Este correo electrónico, así como los archivos adjuntos que contenga, es confidencial de conformidad con las leyes aplicables, y es para uso exclusivo del destinatario al que expresamente se le ha enviado. Si usted no es el destinatario legítimo del mismo,
                                        deberá reportarlo al remitente del correo y borrarlo inmediatamente. Cualquier revisión, retransmisión, divulgación, difusión o cualquier otro uso de este correo, por personas o entidades distintas a las del destinatario legítimo,
                                        queda expresamente prohibido. Los derechos de propiedad respecto de la información, material, documentos electrónicos, bases de datos, diseños, y los distintos elementos en él contenidos, son titularidad de alguna de las empresas
                                        de Grupo Santillana en México. Este correo electrónico, no pretende ni debe ser considerado como constitutivo de ninguna relación legal, contractual o de otra índo le similar.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 15px 10px 15px 10px;">

                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <!--[if (gte mso 9)|(IE)]>
                                </td>
                            </tr>
                        </table>
                        <![endif]-->
                </body>

                </html>

        ';

    }

}
