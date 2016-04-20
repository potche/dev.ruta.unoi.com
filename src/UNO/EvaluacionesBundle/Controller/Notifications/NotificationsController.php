<?php

namespace UNO\EvaluacionesBundle\Controller\Notifications;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Created by PhpStorm.
 * User: jbravob
 * Date: 15/12/15
 * Time: 12:27 PM
 */
class NotificationsController extends Controller {

    public function testAction(Request $request){

        if(!Utils::isUserLoggedIn($request->getSession())){

            $this->redirectToRoute('login');

        }

        if(!Utils::isUserAdmin($request->getSession())){

            throw new AccessDeniedHttpException("Sin autorización para realizar esta acción");
        }

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


    public function mailingstatusAction(Request $request){

        $session =  $request->getSession();

        $personId = $request->request->get('personid');
        $status = $request->request->get('status');
        $response = new JsonResponse();

        if (!$personId || !$status) {

            $response->setData(array(
                'status' => '400',
                'message' => 'Petición malformada'
            ));
        }else{

            $em = $this->getDoctrine()->getManager();
            $person = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Person')
                ->findOneBy(array(
                    'personid' => $personId
                ));

            $person->setMailing($status === 'true' ? 1 : 0);
            $em->flush();
            $session->set('mailing',$status === 'true' ? 1 : 0);

            $response->setData(array(
                'status' => '200',
                'message' => 'Se han cambiado tus preferencias con éxito'
            ));
        }

        return $response;
    }

    public function urldisablemailingAction(Request $request, $personid){

        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $person = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Person')
            ->findOneBy(array(
                'personid' => $personid
            ));

        if(!$person){

            throw new BadRequestHttpException('Petición malformada');
        }

        $person->setMailing(0);
        $em->flush();
        $session->set('mailing',0);
        return new Response('Se han cambiado tus preferencias con éxito',200);
    }

}