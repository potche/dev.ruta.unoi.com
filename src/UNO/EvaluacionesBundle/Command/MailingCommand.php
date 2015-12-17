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
        $mesg = '';

        switch($frequency){

            case 'daily':

                $mesg = $this->buildMessage(
                        'Resumen diario',
                        'UNOEvaluacionesBundle:Notifications:newSurvey.html.twig',
                        array(
                            'name' => 'Julio'
                        ),
                        $this->getDailyRecipients()
                    );
                break;

            case 'weekly':

                $this->buildMessage(
                    'Resumen Semanal',
                    'UNOEvaluacionesBundle:Notifications:newSurvey.html.twig',
                    array(
                        'name' => 'Julio'
                    ),
                    $this->getWeeklyRecipients()
                );
                break;

            default:

                $output->writeln('Opción no disponible');
                break;
        }

        $output->writeln($mesg);
    }

    private function buildMessage($title, $view, $params, $recipients){

        $message = \Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom('noreplymx@unoi.com')
            ->setTo($recipients)
            ->setBody(
                $this->getContainer()->get('templating')->render($view, $params),
                'text/html'
            );
        $i = $this->getContainer()->get('mailer')->send($message);

        return 'Finalizado. Se enviaron '.$i.' mensajes';
    }

    private function getDailyRecipients(){

        return array('jbravo@clb.unoi.com','bovarbj90@gmail.com');
    }

    private function getWeeklyRecipients(){

        return array('jbravo@clb.unoi.com');
    }
}