<?php

namespace UNO\EvaluacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UNO\EvaluacionesBundle\Entity\Optionapplication;
use UNO\EvaluacionesBundle\Entity\Privilege;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/test/menu")
     * @Template()
     */
    public function testAction()
    {
        $em = $this->getDoctrine()->getManager();
        $Privilege = new Privilege();
        $Privilege->setOptionApplicationId(1);
        $Privilege->setProfileId(26);

        $em->persist($Privilege);
        $em->flush();


        /*
        $em = $this->getDoctrine()->getManager();
        $OptionApplication = new Optionapplication();
        $OptionApplication->setNameOptionApplication('prueba');
        $OptionApplication->setDescOptionApplication('desc de prueba');
        $OptionApplication->setRuteOptionApplication('/prueba');
        //$OptionApplication->setStatusOptionApplication(1);

        $em->persist($OptionApplication);
        $em->flush();
*/
        $em = $this->getDoctrine()->getManager();
        $UserValidationEmail = $em->getRepository('UNOEvaluacionesBundle:Optionapplication')->findOneBy(array('optionApplicationId' => 1));
        if (!empty($UserValidationEmail)) {
            print_r($UserValidationEmail);
        }

        echo "<hr/>";

        $em = $this->getDoctrine()->getManager();
        $UserValidationEmail = $em->getRepository('UNOEvaluacionesBundle:Privilege')->findOneBy(array('optionApplicationId' => 1));
        if (!empty($UserValidationEmail)) {
            print_r($UserValidationEmail);
        }

        return new Response('insertando.....');
    }
}