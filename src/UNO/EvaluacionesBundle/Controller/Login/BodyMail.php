<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 17/09/15
 * Time: 10:28 AM
 * Class Login
 * @package UNO\EvaluacionesBundle\Controller\Login
 */

namespace UNO\EvaluacionesBundle\Controller\Login;

class BodyMail{

    public function run($nombre, $codigo, $url) {
        $aviso = "Este correo electrónico, así como los archivos adjuntos que contenga, es confidencial de conformidad con las leyes aplicables, y es para uso exclusivo del destinatario al que expresamente se le ha enviado. Si usted no es el destinatario legítimo del mismo, deberá reportarlo al remitente del correo y borrarlo inmediatamente. Cualquier revisión, retransmisión, divulgación, difusión o cualquier otro uso de este correo, por personas o entidades distintas a las del destinatario legítimo, queda expresamente prohibido. Los derechos de propiedad respecto de la información, material, documentos electrónicos, bases de datos, diseños, y los distintos elementos en él contenidos, son titularidad de alguna de las empresas de Grupo Santillana en México. Este correo electrónico, no pretende ni debe ser considerado como constitutivo de ninguna relación legal, contractual o de otra índole similar.";

        return '<!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="initial-scale=1.0"/>
                            <meta name="format-detection" content="telephone=no"/>
                            <title></title>
                            <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,100,400,600" rel="stylesheet" type="text/css">
                            <style type="text/css">'.
                             $this->getCss()
                            .'</style>
                        </head>

                        <body style="font-size:12px; font-family:Open Sans, Arial,Tahoma, Helvetica, sans-serif; background-color:#ededed; ">
                            <!--start 100% wrapper (white background) -->
                            <table width="100%" id="mainStructure" border="0" cellspacing="0" cellpadding="0" style="background-color:#ededed;">
                                <!-- START VIEW HEADER -->
                                <tr>
                                    <td align="center" valign="top" style="background-color: #59AAC2; ">
                                        <!-- start container 600 -->
                                        <table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="container" bgcolor="#59AAC2" style="background-color: #59AAC2; ">
                                            <tr>
                                                <td valign="top">
                                                    <table width="560" align="center" border="0" cellspacing="0" cellpadding="0" class="full-width" bgcolor="#59AAC2" style="background-color: #59AAC2; ">
                                                        <!-- start space -->
                                                        <tr>
                                                            <td valign="top" height="10">
                                                            </td>
                                                        </tr>
                                                        <!-- end space -->
                                                        <tr>
                                                            <td valign="top">
                                                                <!-- start container -->
                                                                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td valign="top">
                                                                            <!-- start view online -->
                                                                            <table align="left" border="0" cellspacing="0" cellpadding="0" class="container2">
                                                                                <tr>
                                                                                    <td>
                                                                                        <table align="center" border="0" cellspacing="0" cellpadding="0">
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <a href=""><img src="https://ruta.unoi.com/public/assets/images/login/logoUnoHome.png" style="width:25%; margin-left: 5%;"/> </a>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                                <!-- start space -->
                                                                                <tr>
                                                                                    <td valign="top" class="increase-Height">
                                                                                    </td>
                                                                                </tr>
                                                                                <!-- end space -->
                                                                            </table>
                                                                            <!-- end view online -->
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <!-- end container  -->
                                                            </td>
                                                        </tr>
                                                        <!-- start space -->
                                                        <tr>
                                                            <td valign="top" height="10">
                                                            </td>
                                                        </tr>
                                                        <!-- end space -->

                                                        <!-- start space -->
                                                        <tr>
                                                            <td valign="top" class="increase-Height">
                                                            </td>
                                                        </tr>
                                                        <!-- end space -->

                                                    </table>
                                                    <!-- end container 600-->
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                                <!-- END VIEW HEADER -->

                                <!--START TOP NAVIGATION ​LAYOUT-->
                                <tr>
                                    <td valign="top">
                                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                                            <!-- START CONTAINER NAVIGATION -->
                                            <tr>
                                                <td height="30">

                                                </td>
                                            </tr>
                                            <!-- END CONTAINER NAVIGATION -->
                                        </table>
                                    </td>
                                </tr>
                                <!--END TOP NAVIGATION ​LAYOUT-->

                                <!-- START HEIGHT SPACE 20PX LAYOUT-1 -->
                                <tr>
                                    <td valign="top" align="center" class="fix-box">
                                        <table width="600" height="20" align="center" border="0" cellspacing="0" cellpadding="0"
                                               style="background-color: #ffffff; border-top-left-radius: 4px; border-top-right-radius: 4px;"
                                               class="full-width">
                                            <tr>
                                                <td valign="top" height="20">
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- END HEIGHT SPACE 20PX LAYOUT-1-->

                                <!-- START EMAIL CONTENT -->

                                <tr>
                                    <td align="center" valign="top"  class="fix-box">
                                        <!-- start container width 600px -->
                                        <table width="600"  align="center" border="0" cellspacing="0" cellpadding="0" class="container" bgcolor="#ffffff" style="background-color: #ffffff; border-top-left-radius: 4px; border-top-right-radius: 4px;">
                                            <tr>
                                                <td valign="top">
                                                    <!-- start container width 560px -->
                                                    <table width="560"  align="center" border="0" cellspacing="0" cellpadding="0" class="full-width" bgcolor="#ffffff" style="background-color:#ffffff;">
                                                        <!-- start image content -->
                                                        <tr>
                                                            <td valign="top" width="100%">
                                                                <!-- start content left -->
                                                                <table width="270" border="0" cellspacing="0" cellpadding="0" align="center" class="full-width"  >
                                                                    <!-- start text content -->
                                                                    <tr>
                                                                        <td valign="top">
                                                                            <table border="0" cellspacing="0" cellpadding="0" align="center" >
                                                                                <tr>
                                                                                    <td  style="font-size: 14px; line-height: 22px; font-family:Open Sans, Arial,Tahoma, Helvetica, sans-serif; color:#555555; font-weight:300; text-align:justify;">
                                                                                        <span style="color: #555555; font-weight: 300;">
                                                                                            <p>Estimad@ <strong>' . $nombre . '</strong></p>

                                                                                            <div>Solamente falta validar su correo en <strong> UNO internacional</strong></div>
                                                                                            <br>
                                                                                            <!--<p>
                                                                                                C&oacute;digo de validaci&oacute;n: <strong style="font-size:20px;">'.$codigo.'</strong>
                                                                                            </p>-->
                                                                                            <h3 style="text-align:center; background-color: #59AAC2; padding: 15px;"><a class="btn btn-success" style="color:white; text-decoration:none;" href="' . $url . '"> Validar ahora </a></h3>
                                                                                            <p>En caso de tener alg&uacute;n problema puede copiar y pegar la siguiente direcci&oacute;n en su navegador</p>
                                                                                            <h5>' . $url . '</h5>
                                                                                            <br/><br/>
                                                                                        </span>
                                                                                    </td>
                                                                                </tr>
                                                                                <!--start space height -->
                                                                                <tr>
                                                                                    <td height="20" ></td>
                                                                                </tr>
                                                                                <!--end space height -->
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    <!-- end text content -->
                                                                </table>
                                                                <!-- end content left -->
                                                            </td>
                                                        </tr>
                                                        <!-- end image content -->
                                                    </table>
                                                    <!-- end container width 560px -->
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- end  container width 600px -->
                                    </td>
                                </tr>
                                <!-- START NOTIFICATION/ACTIVITY CONTENT-->

                                <!-- END EMAIL CONTENT -->


                                <!--START FOOTER LAYOUT-->
                                <tr>
                                    <td valign="top">
                                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                                            <!-- START CONTAINER  -->
                                            <tr>
                                                <td align="center" valign="top">
                                                    <!-- start footer container -->
                                                    <table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="container">
                                                        <tr>
                                                            <td valign="top">
                                                                <!-- start footer -->
                                                                <table width="560" align="center" border="0" cellspacing="0" cellpadding="0" class="full-width">
                                                                    <!-- start space -->
                                                                    <tr>
                                                                        <td valign="top" height="20">
                                                                        </td>
                                                                    </tr>
                                                                    <!-- end space -->
                                                                    <tr>
                                                                        <td valign="middle">
                                                                            <table align="center" border="0" cellspacing="0" cellpadding="0" class="container2">
                                                                                <tr>
                                                                                    <td align="center" valign="top"
                                                                                        style="font-size: 11px;  line-height: 18px; font-weight:300; text-align: center; font-family:Open Sans,Arail,Tahoma, Helvetica, Arial, sans-serif;">
                                                                                        <span style="text-decoration: none; color: #a3a2a2;"><a
                                                                                                href="www.uno-internacional.com"
                                                                                                style="text-decoration: none; color: #a3a2a2;">UNO Internacional &reg;</a> </span>
                                                                                        <br/>
                                                                                        <h3 class="aviso"><strong>Aviso Legal</strong></h3>
                                                                                        <p class="aviso" style="text-align: justify;">
                                                                                            '.$aviso.'
                                                                                        </p>
                                                                                    </td>

                                                                                </tr>

                                                                                <!-- start space -->
                                                                                <tr>
                                                                                    <td valign="top" class="increase-Height-20">
                                                                                    </td>
                                                                                </tr>
                                                                                <!-- end space -->
                                                                            </table>
                                                                        </td>
                                                                    </tr>

                                                                    <!-- start space -->
                                                                    <tr>
                                                                        <td valign="top" height="20">
                                                                        </td>
                                                                    </tr>
                                                                    <!-- end space -->
                                                                </table>
                                                                <!-- end footer -->
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- end footer container -->
                                                </td>
                                            </tr>

                                            <!-- END CONTAINER  -->

                                        </table>
                                    </td>
                                </tr>
                                <!--END FOOTER ​LAYOUT-->

                            </table>
                        </body>
                    </html>
        ';

    }

    private function getCss(){
        return '
            .aviso {
                font-size: 10px;
                color: gray;
                font-family: consolas;
                padding: 10px;
            }
            
            .ReadMsgBody {
                width: 100%;
                background-color: #ffffff;
            }
            
            .ExternalClass {
                width: 100%;
                background-color: #ffffff;
            }
            
            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
                line-height: 100%;
            }
            
            html {
                width: 100%;
            }
            
            body {
                -webkit-text-size-adjust: none;
                -ms-text-size-adjust: none;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            table {
                border-spacing: 0;
            }
            
            img {
                display: block !important;
            }
            
            table td {
                border-collapse: collapse;
            }
            
            .yshortcuts a {
                border-bottom: none !important;
            }
            
            html, body {
                background-color: #ededed;
                margin: 0;
                padding: 0;
            }
            
            img {
                height: auto;
                line-height: 100%;
                outline: none;
                text-decoration: none;
                display: block;
            }
            
            br, strong br, b br, em br, i br {
                line-height: 100%;
            }
            
            h1, h2, h3, h4, h5, h6 {
                line-height: 100% !important;
                -webkit-font-smoothing: antialiased;
            }
            
            h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
                color: #6fdbe8 !important;
            }
            
            h1 a:active, h2 a:active, h3 a:active, h4 a:active, h5 a:active, h6 a:active {
                color: #6fdbe8 !important;
            }
            
            h1 a:visited, h2 a:visited, h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
                color: #6fdbe8 !important;
            }
            
            table td, table tr {
                border-collapse: collapse;
            }
            
            .yshortcuts, .yshortcuts a, .yshortcuts a:link, .yshortcuts a:visited, .yshortcuts a:hover, .yshortcuts a span {
                color: black;
                text-decoration: none !important;
                border-bottom: none !important;
                background: none !important;
            }
            
            code {
                white-space: normal;
                word-break: break-all;
            }
            
            span a {
                text-decoration: none !important;
            }
            
            a {
                text-decoration: none !important;
            }
            
            .default-edit-image {
                height: 20px;
            }
            
            .nav-ul {
                margin-left: -23px !important;
                margin-top: 0px !important;
                margin-bottom: 0px !important;
            }
            
            img {
                height: auto !important;
            }
            
            td[class="image-270px"] img {
                width: 270px !important;
                height: auto !important;
                max-width: 270px !important;
            }
            
            td[class="image-170px"] img {
                width: 170px !important;
                height: auto !important;
                max-width: 170px !important;
            }
            
            td[class="image-185px"] img {
                width: 185px !important;
                height: auto !important;
                max-width: 185px !important;
            }
            
            td[class="image-124px"] img {
                width: 124px !important;
                height: auto !important;
                max-width: 124px !important;
            }
            
            @media only screen and (max-width: 640px) {
                body {
                    width: auto !important;
                }
            
                table[class="container"] {
                    width: 100% !important;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                td[class="image-270px"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                }
            
                td[class="image-170px"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                }
            
                td[class="image-185px"] img {
                    width: 185px !important;
                    height: auto !important;
                    max-width: 185px !important;
                }
            
                td[class="image-124px"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                }
            
                td[class="image-100-percent"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                }
            
                td[class="small-image-100-percent"] img {
                    width: 100% !important;
                    height: auto !important;
                }
            
                table[class="full-width"] {
                    width: 100% !important;
                }
            
                table[class="full-width-text"] {
                    width: 100% !important;
                    background-color: #ffffff;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                table[class="full-width-text2"] {
                    width: 100% !important;
                    background-color: #ffffff;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                table[class="col-2-3img"] {
                    width: 50% !important;
                    margin-right: 20px !important;
                }
            
                table[class="col-2-3img-last"] {
                    width: 50% !important;
                }
            
                table[class="col-2-footer"] {
                    width: 55% !important;
                    margin-right: 20px !important;
                }
            
                table[class="col-2-footer-last"] {
                    width: 40% !important;
                }
            
                table[class="col-2"] {
                    width: 47% !important;
                    margin-right: 20px !important;
                }
            
                table[class="col-2-last"] {
                    width: 47% !important;
                }
            
                table[class="col-3"] {
                    width: 29% !important;
                    margin-right: 20px !important;
                }
            
                table[class="col-3-last"] {
                    width: 29% !important;
                }
            
                table[class="row-2"] {
                    width: 50% !important;
                }
            
                td[class="text-center"] {
                    text-align: center !important;
                }
            
                table[class="remove"] {
                    display: none !important;
                }
            
                td[class="remove"] {
                    display: none !important;
                }
            
                table[class="fix-box"] {
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                td[class="fix-box"] {
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                td[class="font-resize"] {
                    font-size: 18px !important;
                    line-height: 22px !important;
                }
            
                table[class="space-scale"] {
                    width: 100% !important;
                    float: none !important;
                }
            
                table[class="clear-align-640"] {
                    float: none !important;
                }
            
            }
            
            @media only screen and (max-width: 479px) {
                body {
                    font-size: 10px !important;
                }
            
                table[class="container"] {
                    width: 100% !important;
                    padding-left: 10px !important;
                    padding-right: 10px !important;
                }
            
                table[class="container2"] {
                    width: 100% !important;
                    float: none !important;
            
                }
            
                td[class="full-width"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
                td[class="image-270px"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
                td[class="image-170px"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
                td[class="image-185px"] img {
                    width: 185px !important;
                    height: auto !important;
                    max-width: 185px !important;
                    min-width: 124px !important;
                }
            
                td[class="image-124px"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
                td[class="image-100-percent"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
                td[class="small-image-100-percent"] img {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
                table[class="full-width"] {
                    width: 100% !important;
                }
            
                table[class="full-width-text"] {
                    width: 100% !important;
                    background-color: #ffffff;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                table[class="full-width-text2"] {
                    width: 100% !important;
                    background-color: #ffffff;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            
                table[class="col-2-footer"] {
                    width: 100% !important;
                    margin-right: 0px !important;
                }
            
                table[class="col-2-footer-last"] {
                    width: 100% !important;
                }
            
                table[class="col-2"] {
                    width: 100% !important;
                    margin-right: 0px !important;
                }
            
                table[class="col-2-last"] {
                    width: 100% !important;
            
                }
            
                table[class="col-3"] {
                    width: 100% !important;
                    margin-right: 0px !important;
                }
            
                table[class="col-3-last"] {
                    width: 100% !important;
            
                }
            
                table[class="row-2"] {
                    width: 100% !important;
                }
            
                table[id="col-underline"] {
                    float: none !important;
                    width: 100% !important;
                    border-bottom: 1px solid #eee;
                }
            
                td[id="col-underline"] {
                    float: none !important;
                    width: 100% !important;
                    border-bottom: 1px solid #eee;
                }
            
                td[class="col-underline"] {
                    float: none !important;
                    width: 100% !important;
                    border-bottom: 1px solid #eee;
                }
            
                td[class="text-center"] {
                    text-align: center !important;
            
                }
            
                div[class="text-center"] {
                    text-align: center !important;
                }
            
                table[id="clear-padding"] {
                    padding: 0 !important;
                }
            
                td[id="clear-padding"] {
                    padding: 0 !important;
                }
            
                td[class="clear-padding"] {
                    padding: 0 !important;
                }
            
                table[class="remove-479"] {
                    display: none !important;
                }
            
                td[class="remove-479"] {
                    display: none !important;
                }
            
                table[class="clear-align"] {
                    float: none !important;
                }
            
                table[class="width-small"] {
                    width: 100% !important;
                }
            
                table[class="fix-box"] {
                    padding-left: 0px !important;
                    padding-right: 0px !important;
                }
            
                td[class="fix-box"] {
                    padding-left: 0px !important;
                    padding-right: 0px !important;
                }
            
                td[class="font-resize"] {
                    font-size: 14px !important;
                }
            
                td[class="increase-Height"] {
                    height: 10px !important;
                }
            
                td[class="increase-Height-20"] {
                    height: 20px !important;
                }
            
            }
            
            @media only screen and (max-width: 320px) {
                table[class="width-small"] {
                    width: 125px !important;
                }
            
                img[class="image-100-percent"] {
                    width: 100% !important;
                    height: auto !important;
                    max-width: 100% !important;
                    min-width: 124px !important;
                }
            
            }';
    }

}