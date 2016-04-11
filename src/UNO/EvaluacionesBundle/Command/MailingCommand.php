<?php

namespace UNO\EvaluacionesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//Tag utilizado en Mandrill para resumenes semanales
DEFINE('TAG_WEEKLY_BRIEF', 'semanal-diagnostico-dir');
//Tag utilizado en Mandrill para notificaciones diarias
DEFINE('TAG_DAILY_NOTIFICATION', 'diario-diagnostico-notif');
//Número de días antes de hoy que se revisan para buscar nuevas evaluaciones
DEFINE('DAYS_SINCE_NEW_SURVEY','1');
//Parámetros requeridos por el comando para reconocimiento de host y rutas de recursos
DEFINE('HOST','dev.ruta.unoi.com');
DEFINE('SCHEME','http');

/**
 * Class MailingCommand
 * @package UNO\EvaluacionesBundle\Command
 * @author jbravo
 *
 * Clase que contiene los métodos para el envío de correos de la Plataforma de Diagóstico Institucional, hereda los métodos de ContanierAwareCommand,
 * la clase de Symfony para la ejecución de comandos via consola.
 *
 * Se requiere hacerlo de esta forma para facilitar la ejecución de envío de mensajes a través de tareas del cron.
 *
 */

class MailingCommand extends ContainerAwareCommand{

    /**
     * Configuración del comando
     */
    protected function configure()
    {
        $this
            ->setName('mailing:send')
            ->setDescription('Interfaz de notificaciones')
            ->addArgument(
                'frequency',
                InputArgument::REQUIRED,
                'Tipo de mensaje a enviar'
            );
    }

    /**
     *
     * Método de ejecución del comando
     *
     * Symfony acepta parámetros tanto de entrada como de salida para manejar los argumentos del comando y la salida a consola
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost(HOST);
        $context->setScheme(SCHEME);

        $frequency = $input->getArgument('frequency');

        switch($frequency){

            case 'daily':

                $this->dailyBriefNewNotifications($output);
                break;

            case 'weekly':

                $this->weeklyBriefDirector($output);
                break;

            default:

                $output->writeln('Opción no disponible');
                break;
        }

        $output->writeln('Proceso de envío de notificaciones finalizado');
    }

    /**
     * Construcción de resumen diario de nuevas notificaciones para cualquier usuario registrado
     *
     * @param $output "Objeto de Comando de Symfony para mandar a consola el resultado de ejecución"
     */

    protected function dailyBriefNewNotifications($output){

        $response = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APINotificationsNewSurveys',array('daysago'=>DAYS_SINCE_NEW_SURVEY),true), false), true);

        if(!isset ($response['Error'])){

            foreach ($response as $p) {

                if($p['Email'] != '' && $p['Email'] != null){

                    $mesg = $this->buildMessage(
                        'Resumen diario',
                        'UNOEvaluacionesBundle:Notifications:newSurvey.html.twig',
                        array(
                            'persona' => $p['Persona'],
                            'name' => $p['Nombre'],
                            'surveys' => $p['NewSurveys'],
                            'host' => HOST,
                            'scheme' => SCHEME
                        ),
                        $p['Email'],
                        TAG_DAILY_NOTIFICATION);

                    $output->writeln("Se envió ".$mesg." mensaje a ".$p['Email']);

                } else{

                    $output->writeln("El contacto ".$p['Nombre']." carece de información a presentar, no se ha enviado nada");
                }
            }
        }
    }

    /**
     * Construcción de resumen semanal del director.
     *
     * @param $output "Objeto de Comando de Symfony para mandar a consola el resultado de ejecución"
     */

    protected function weeklyBriefDirector($output){

        $dirs = $this->obtenDirectores();

        if(!$dirs){

            $output->writeln('No se encontraron usuarios con notificaciones activas para el envío de mensaje');

        }else{

            foreach($dirs as $d){

                $top5 = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APINotificationsTopPersons',array('schoolid'=>$d['idescuela']),true), false), true);
                $progressSchool = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APIStatsProgressBySchool',array('schoolid'=>$d['idescuela']),true), false), true);
                $progressPerson = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APIStatsProgressByPerson',array('personid'=>$d['persona']),true), false), true);

                if($d['email'] != '' && $d['email'] != null && !isset($progressPerson['Error']) && !isset($progressSchool['Error'])){

                    $mesg = $this->buildMessage(
                        'Resumen semanal',
                        'UNOEvaluacionesBundle:Notifications:weeklyBrief2.html.twig',
                        array(
                            'persona' => $d['persona'],
                            'name' => $d['nombre'],
                            'top5' => $top5,
                            'progressSchool' => $progressSchool,
                            'progressPerson' => $progressPerson,
                            'host' => HOST,
                            'scheme' => SCHEME
                        ),
                        $d['email'],
                        TAG_WEEKLY_BRIEF);

                    $output->writeln("Se envió ".$mesg." mensaje a ".$d['email']);

                } else{

                    $output->writeln("El contacto ".$d['nombre']." carece de E-mail, no se ha enviado nada");
                }
            }
        }
    }

    /**
     *
     * Construcción de mensaje para correo
     *
     * @param $title "Titulo del mensaje"
     * @param $view "Enlace de la vista que ocupara el mensaje"
     * @param $params "Parámetros de la vista"
     * @param $recipient "Dirección de correo electrónico de quien recibe el mensaje"
     * @return int "Cantidad de correos enviados"
     * @throws \Exception
     * @throws \Twig_Error
     */

    private function buildMessage($title, $view, $params, $recipient,$tag){

        setlocale(LC_ALL,"es_MX.utf8");
        $sendDate = strftime("%d %B %Y");

        $message = \Swift_Message::newInstance()
            ->setSubject($title.' '.$sendDate)
            ->setFrom(array('noreplymx@unoi.com' => 'Diagnóstico UNOi (DEMO)'))
            ->setTo($recipient)
            ->setBody(
                $this->getContainer()->get('templating')->render($view, $params),
                'text/html'
            );

        $message->getHeaders()->addTextHeader('X-MC-Tags',$tag);

        return $this->getContainer()->get('mailer')->send($message);
    }

    /**
     * Obtención de personas con perfil de director y que tengan habilitado el envío de notificaciones
     * @return mixed "Devuelve un arreglo con los directores encontrados"
     */

    private function obtenDirectores(){

        $em = $this->getContainer()->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();

        //ToDo: descomentar esta parte cuando se terminen de hacer simulaciones

        /*$directores = $qb->select("p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre, s.schoolid as idescuela, s.school as escuela")
            ->from('UNOEvaluacionesBundle:School','s')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.schoolid = s.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr','WITH','ps.profileid = pr.profileid AND pr.profilecode = :profile')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid AND p.mailing = 1')
            ->setParameter('profile', 'DIR')
            ->groupBy('p.personid')
            ->getQuery()
            ->getResult();*/

        $directores = array();

        array_push($directores,array(
            'persona'=>'1151123',
            'email'=>'bovarbj90@gmail.com',
            'nombre'=>'Carlos Blé',
            'idescuela' => '1087',
            'escuela'=>'SOLUCIONES DIGITALES'
            ));

        return $directores;
    }
}