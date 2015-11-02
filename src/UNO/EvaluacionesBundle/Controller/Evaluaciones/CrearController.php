<?php
/**
 * Created by PhpStorm.
 * User: julzone
 * Date: 22/10/15
 * Time: 11:27 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Evaluaciones;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use UNO\EvaluacionesBundle\Entity\Answer;
use UNO\EvaluacionesBundle\Entity\Question;
use UNO\EvaluacionesBundle\Entity\Questionxsurvey;
use UNO\EvaluacionesBundle\Entity\Survey;

class CrearController extends Controller {

    /**
     *
     * Método principal del controlador para crear evaluaciones
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedHttpException
     * @author julio
     * @version 0.2.0
     */

    public function indexAction(Request $request){

        /**
         * Manejo de sesión
         */

        $session = $request->getSession();

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }


        return $this->render('UNOEvaluacionesBundle:Crear:crear.html.twig',array(
            'categories' => $this->getCategories(),
            'questions' => array_column($this->getQuestions(),'question'),
            'profiles' => $this->getProfiles(),
            'levels' => $this->getLevels(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws InternalErrorException
     *
     * Método invocado para almacenar una nueva evaluación en la bd
     *
     */

    public function saveAction(Request $request){

        $session = $request->getSession();
        $eval = $request->request->get('eval');

        if (!Utils::isUserLoggedIn($session)) {

            return $this->redirectToRoute('login');
        }

        if(!Utils::isUserAdmin($session)){

            throw new AccessDeniedHttpException('No estás autorizado para realizar esta acción');
        }

        if(!$eval) {

            throw new InternalErrorException('Ha ocurrido un error al procesar esta petición');
        }

        $em = $this->getDoctrine()->getManager();

        $survey = new Survey();
        $survey->setActive(true);
        $survey->setClosingdate(new \DateTime($eval['closingdate'].' 23:59'));
        $survey->setCreatedby($session->get('nameS'));
        $survey->setCreationdate(new \DateTime());
        $survey->setModifieddate(new \DateTime());
        $survey->setTitle($eval['title']);
        $survey->setDescription($eval['description']);


        /*$em->persist($survey);
        $em->flush();*/

        //if($survey->getSurveyid() != null){

            foreach ($eval['preguntas'] as $i => $pregunta) {

                $q_form = explode('::',$pregunta);

                // Primero busco si existe la pregunta para no tener datos redundantes
                $q = $this->getDoctrine()
                    ->getRepository('UNOEvaluacionesBundle:Question')
                    ->findBy(array(
                    'question' => $q_form[1]
                    ));

                if(!$q[0]){

                    $q = new Question();
                    $q->setQuestion($q_form[1]);
                    $q->setRequired(true);
                    $q->setSubcategorySubcategoryid($this->getDoctrine()
                        ->getRepository('UNOEvaluacionesBundle:Subcategory')
                        ->find($q_form[0]));
                    $q->setType('M');

                    /*$em->persist($q);
                    $em->flush();*/
                }
                else{

                    $q[0]->setSubcategorySubcategoryid($this->getDoctrine()
                        ->getRepository('UNOEvaluacionesBundle:Subcategory')
                        ->find($q_form[0]));

                    $em->flush();
                }
            }
        //}
        
        /**
         * ToDo: implementar lógica para almacenar evaluación a partir de la petición
         */

        return new Response('Insertada');
        //return $this->redirectToRoute('evaluaciones');
    }

    private function getCategories(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $subcats = $qb->select('sub.subcategoryid, sub.subcategory')
            ->from('UNOEvaluacionesBundle:Subcategory','sub')
            ->orderBy('sub.subcategory')
            ->getQuery()
            ->getResult();

        return $subcats;

    }

    private function getQuestions(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $questions = $qb->select('q.question')
            ->from('UNOEvaluacionesBundle:Question','q')
            ->orderBy('q.question')
            ->getQuery()
            ->getResult();

        return $questions;

    }

    private function getLevels(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $levels = $qb->select('lev.schoollevelid, lev.schoollevel')
            ->from('UNOEvaluacionesBundle:Schoollevel','lev')
            ->orderBy('lev.schoollevel')
            ->getQuery()
            ->getResult();

        return $levels;
    }

    private function getProfiles(){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $profiles = $qb->select('pr.profileid, pr.profile')
            ->from('UNOEvaluacionesBundle:Profile','pr')
            ->where('pr.profileid > 1')
            ->orderBy('pr.profile')
            ->getQuery()
            ->getResult();

        return $profiles;
    }

}