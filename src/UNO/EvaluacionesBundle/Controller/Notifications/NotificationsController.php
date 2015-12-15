<?php

namespace UNO\EvaluacionesBundle\Controller\Notifications;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: jbravob
 * Date: 15/12/15
 * Time: 12:27 PM
 */
class NotificationsController extends Controller {

    public function testAction(){

        $message = \Swift_Message::newInstance()
            ->setSubject('Mail de prueba')
            ->setFrom('noreplymx@unoi.com')
            ->setTo('jbravo@clb.unoi.com')
            ->setBody(
                $this->renderView(
                    'UNOEvaluacionesBundle:Notifications:newSurvey.html.twig',
                    array('name' => 'JULZ')
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        return new Response('Mail sent',200);
    }

}