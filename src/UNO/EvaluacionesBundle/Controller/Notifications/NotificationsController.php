<?php

namespace UNO\EvaluacionesBundle\Controller\Notifications;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: jbravob
 * Date: 15/12/15
 * Time: 12:27 PM
 */
class NotificationsController extends Controller {

    public function testAction(){

        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'mailing:send',
            'frequency' => 'weekly',
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        return new Response($content,200);
    }

}