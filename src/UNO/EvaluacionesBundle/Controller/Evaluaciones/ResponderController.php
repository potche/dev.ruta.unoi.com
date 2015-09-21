<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 18/09/15
 * Time: 12:33
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Responder controller.
 *
 * @Route("/responder")
 */

class ResponderController extends Controller
{
    public function indexAction(){



        //$em = $this->getDoctrine()->getEntityManager();

        $repository = $this->getDoctrine()->getRepository('UNOEvaluacionesBundle:Option');

        $query = $repository->createQueryBuilder('opt')
            /*->where('p.price > :price')
            ->setParameter('price', '19.99')
            ->orderBy('p.price', 'ASC')
            */
            ->getQuery();

        $options = $query->getResult();

        return $this->render('UNOEvaluacionesBundle:Evaluaciones:responder.html.twig',array( 'options'=>$options ));
    }
}