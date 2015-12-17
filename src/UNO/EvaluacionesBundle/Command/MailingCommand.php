<?php

namespace UNO\EvaluacionesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class MailingCommand extends ContainerAwareCommand{

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

    protected function execute(InputInterface $input, OutputInterface $output){

        $frequency = $input->getArgument('frequency');

        switch($frequency){

            case 'daily':

                $this->dailyBriefNewNotifications($output);
                break;

            case 'weekly':

                $this->getWeeklyRecipients($output);
                break;

            default:

                $output->writeln('Opción no disponible');
                break;
        }

        $output->writeln('Proceso de envío de notificaciones finalizado');
    }

    protected function dailyBriefNewNotifications($output){

        $response = json_decode(file_get_contents($this->getContainer()->get('router')->generate('APINotificationsNewSurveys',array('daysago'=>'1'),true), false), true);

        foreach ($response as $p) {

            if($p['Email'] != '' && $p['Email'] != null){

                $mesg = $this->buildMessage(
                    'Resumen diario',
                    'UNOEvaluacionesBundle:Notifications:newSurvey.html.twig',
                    array(
                        'name' => $p['Nombre'],
                        'surveys' => $p['NewSurveys']
                    ),
                    $p['Email']
                );

                $output->writeln("Se envió ".$mesg." mensaje a ".$p['Email']);
            }

            else{

                $output->writeln("El contacto ".$p['Nombre']." carece de E-mail, no se ha enviado nada");

            }
        }
    }

    protected function weeklyRecipients($output){

        return array('jbravo@clb.unoi.com');
    }


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
}