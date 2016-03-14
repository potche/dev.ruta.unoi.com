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
use UNO\EvaluacionesBundle\Entity\Answer;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;
use UNO\EvaluacionesBundle\Entity\Question;
use UNO\EvaluacionesBundle\Entity\Questionxsurvey;
use UNO\EvaluacionesBundle\Entity\Survey;
use UNO\EvaluacionesBundle\Entity\Surveyxprofile;

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

            //return $this->redirectToRoute('login');
            return $this->redirectToRoute('login',array(
                'redirect' => 'crear',
                'with' => 'none'
            ));
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
     *
     * Método para almacenar nueva evaluación en BD
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedHttpException
     * @throws InternalErrorException
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

            throw new InternalErrorException('No se han obtenido los datos de la evaluación a crear');
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        try{

            $survey = $this->persistSurvey($eval,$session->get('nameS'),$em);

            $opts = $this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Option')
                ->findAll();

            foreach ($eval['preguntas'] as $i => $pregunta) {

                $q = $this->persistQuestion($pregunta,$em);
                $qxs = $this->persistQuestionInSurvey($i,$q,$survey, $em);
                $this->persistQuestionOptions($qxs, $opts, $em);
            }

            $this->persistSurveyProfiles($eval['perfiles'], $survey, $em);

            $em->getConnection()->commit();

        } catch (\Exception $ex){

            $em->getConnection()->rollback();
            throw new InternalErrorException('Error al agregar la evaluación a la BD, intente nuevamente');
        }

        return $this->redirectToRoute('evaluaciones');
    }

    /**
     *
     * Método para obtener categorías de la BD
     *
     * @return mixed
     */

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

    /**
     *
     * Método para obtener preguntas de la BD
     *
     * @return mixed
     */

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

    /**
     *
     * Método para obtener niveles de la BD
     * @return mixed
     */

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

    /**
     *
     * Método para obtener perfiles de la BD
     * @return mixed
     */

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

    /**
     *
     * Método para insertar una evaluación a la BD
     *
     * @param $eval
     * @param $createdBy
     * @param $em
     * @return Survey
     */

    private function persistSurvey($eval, $createdBy, $em ){

        $survey = new Survey();
        $survey->setActive(true);
        $survey->setClosingdate(new \DateTime($eval['closingdate'].' 23:59'));
        $survey->setCreatedby($createdBy);
        $survey->setCreationdate(new \DateTime());
        $survey->setModifieddate(new \DateTime());
        $survey->setTitle($eval['title']);
        $survey->setDescription($eval['description']);

        $em->persist($survey);
        $em->flush();

        return $survey;
    }

    /**
     *
     * Método para insertar una pregunta a la BD
     *
     * @param $questionData
     * @param $em
     * @return Question
     */

    private function persistQuestion ($questionData, $em) {

        $q_form = explode('::',$questionData);

        // Primero busco si existe la pregunta para no tener datos redundantes
        $q = $this->getDoctrine()
            ->getRepository('UNOEvaluacionesBundle:Question')
            ->findOneBy(array(
                'question' => $q_form[1]
            ));

        if(!$q){

            $q = new Question();
            $q->setQuestion($q_form[1]);
            $q->setRequired(true);
            $q->setSubcategorySubcategoryid($this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Subcategory')
                ->find($q_form[0]));
            $q->setType('M');

            $em->persist($q);
            $em->flush();
        }
        else{
            $q->setSubcategorySubcategoryid($this->getDoctrine()
                ->getRepository('UNOEvaluacionesBundle:Subcategory')
                ->find($q_form[0]));

            $em->flush();
        }
        return $q;
    }

    /**
     *
     * Método para relacionar una pregunta a la evaluación en la BD
     *
     * @param $index
     * @param $q
     * @param $s
     * @param $em
     * @return Questionxsurvey
     */

    private function persistQuestionInSurvey($index, $q, $s, $em){

        $qxs = new Questionxsurvey();
        $qxs->setOrder(1+$index);
        $qxs->setQuestionQuestionid($q);
        $qxs->setSurveySurveyid($s);

        $em->persist($qxs);
        $em->flush();

        return $qxs;
    }

    /**
     *
     * Método para agregar opciones a las preguntas
     *
     * @param $qxs
     * @param $opts
     * @param $em
     */

    private function persistQuestionOptions($qxs, $opts, $em) {

        foreach($opts as $index => $opt) {

            $oxq = new Optionxquestion();
            $oxq->setOrder(1+$index);
            $oxq->setOptionOptionid($opt);
            $oxq->setQuestionxsurvey($qxs);

            $em->persist($oxq);
            $em->flush();
        }
    }

    /**
     *
     * Metodo para relacionar la evaluación con los perfiles y niveles
     *
     * @param $profiles
     * @param $survey
     * @param $em
     */

    private function persistSurveyProfiles($profiles, $survey, $em){

        foreach($profiles as $perf => $p) {

            foreach ($p as $nivel) {

                $sxp = new Surveyxprofile();
                $sxp->setSurveySurveyid($survey);
                $sxp->setProfileProfileid($this->getDoctrine()
                    ->getRepository('UNOEvaluacionesBundle:Profile')
                    ->find($perf)
                );
                $sxp->setSchoollevelid($nivel);
                $em->persist($sxp);
                $em->flush();
            }
        }
    }
}