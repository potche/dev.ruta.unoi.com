<?php

namespace UNO\EvaluacionesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


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
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){

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
     * Método para resumen diario de nuevas notificaciones
     *
     * @param $output
     */

    protected function dailyBriefNewNotifications($output){

        $response = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APINotificationsNewSurveys',array('daysago'=>'1'),true), false), true);

        if(!isset ($response['Error'])){

            foreach ($response as $p) {

                if($p['Email'] != '' && $p['Email'] != null){

                    $mesg = $this->buildMessage(
                        'Resumen diario',
                        'UNOEvaluacionesBundle:Notifications:newSurvey.html.twig',
                        array(
                            'persona' => $p['Persona'],
                            'name' => $p['Nombre'],
                            'surveys' => $p['NewSurveys']
                        ),
                        $p['Email']
                    );

                    $output->writeln("Se envió ".$mesg." mensaje a ".$p['Email']);

                } else{

                    $output->writeln("El contacto ".$p['Nombre']." carece de E-mail, no se ha enviado nada");
                }
            }
        }
    }

    /**
     * Resumen semanal del director
     *
     * @param $output
     */

    protected function weeklyBriefDirector($output){

        $dirs = $this->obtenDirectores();

        if(!$dirs){

            $output->writeln('No se encontraron directores registrados en la plataforma');

        }else{

            foreach($dirs as $d){

                $top5 = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APINotificationsTopPersons',array('schoolid'=>$d['idescuela']),true), false), true);
                $progressSchool = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APIStatsProgressBySchool',array('schoolid'=>$d['idescuela']),true), false), true);
                $progressPerson = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APIStatsProgressByPerson',array('personid'=>$d['persona']),true), false), true);

                if($d['email'] != '' && $d['email'] != null){

                    $mesg = $this->buildMessage(
                        'Resumen semanal',
                        'UNOEvaluacionesBundle:Notifications:weeklyBrief.html.twig',
                        array(
                            'persona' => $d['persona'],
                            'name' => $d['nombre'],
                            'top5' => $top5,
                            'progressSchool' => $progressSchool,
                            'progressPerson' => $progressPerson),
                        $d['email']);

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
     * @param $title
     * @param $view
     * @param $params
     * @param $recipient
     * @return int
     * @throws \Exception
     * @throws \Twig_Error
     */

    private function buildMessage($title, $view, $params, $recipient){

        $message = \Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom('noreplymx@unoi.com')
            ->setTo($recipient)
            ->setBody(
                $this->getContainer()->get('templating')->render($view, $params),
                'text/html'
            );

        return $this->getContainer()->get('mailer')->send($message);
    }

    /**
     * Obtención de personas con perfil de director
     * @return mixed
     */

    private function obtenDirectores(){

        $em = $this->getContainer()->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();

        $directores = $qb->select("p.personid as persona, p.email as email, CONCAT(p.name,' ',p.surname) as nombre, s.schoolid as idescuela, s.school as escuela")
            ->from('UNOEvaluacionesBundle:School','s')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','ps','WITH','ps.schoolid = s.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Profile','pr','WITH','ps.profileid = pr.profileid AND pr.profilecode = :profile')
            ->innerJoin('UNOEvaluacionesBundle:Person','p','WITH','p.personid = ps.personid AND p.mailing = 1')
            ->where('p.personid = 2') // Borrar
            ->setParameter('profile', 'COACH') //Cambiar por DIR
            ->groupBy('p.personid')
            ->getQuery()
            ->getResult();

        return $directores;
    }
}