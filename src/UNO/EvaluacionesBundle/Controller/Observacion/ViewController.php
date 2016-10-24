<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 13/06/16
 * Time: 12:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Observacion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use UNO\EvaluacionesBundle\Controller\Evaluaciones\Utils;

/**
 * Class ViewController
 * @package UNO\EvaluacionesBundle\Controller\Observacion
 */
class ViewController extends Controller{

    private $_profile = array();
    private $status;
    private $message;
    /**
     * @Route("/observacion/view/{observationId}")
     *
     * visiualiza la observacion
     */
    public function indexAction(Request $request, $observationId){
        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseURL = $this->container->get('router')->getContext()->getBaseUrl();
        $baseUrl = $scheme.$host.$baseURL;

        $session = $request->getSession();
        $session->start();
        
        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'inicio',
                'with' => 'none'
            ));
        }

        $this->setProfile($session->get('profileS'));

        if(in_array('COACH',$this->_profile)) {
            $this->valObservationIdBy(array('observationId' => $observationId, 'coachId' => $session->get('personIdS')) );
            $parameters['activities'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/activity/".$observationId, false),true);
        }else{
            $this->valObservationIdBy(array('observationId' => $observationId, 'personId' => $session->get('personIdS')) );
        }

        if($this->status == 200){
            $result = $this->getResQueryOQ($observationId);
            $aspects = $this->getResultAspects($observationId);

            $parameters['questionByCategory'] = $result;
            $parameters['observationId'] = $observationId;
            $parameters['disposition'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/disposition/".$observationId, false),true);
            $parameters['galleries'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/gallery/".$observationId, false),true);
            $parameters['aspects'] = $aspects;

            return $this->render('UNOEvaluacionesBundle:Observacion:view.html.twig', $parameters);

        }else {
            return $this->render('UNOEvaluacionesBundle:Error:error.html.twig', array(
                'status' => $this->status,
                'message' => $this->message
            ));
        }
    }

    /**
     * @param $profileS
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setProfile($profileS){
        $profileJson = json_decode($profileS);
        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    /**
     * @param $observationId
     * @return mixed
     */
    private function getResQueryOQ($observationId){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $obQ = $qb->select("Q.questionid, QS.order, Q.question, Sub.subcategoryid, Sub.subcategory, OA.observationAnswerId, OA.answer, OA.comment, OA.personId, OA.dateRecord")
            ->from('UNOEvaluacionesBundle:Observation', 'O')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS','WITH','QS.surveySurveyid = O.surveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q','WITH','QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub','WITH','Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->leftJoin('UNOEvaluacionesBundle:ObservationAnswer','OA','WITH','OA.questionId = Q.questionid AND O.observationId = OA.observationId')
            ->andWhere('O.observationId = :observationId')
            ->setParameter('observationId', $observationId)
            ->orderBy( 'QS.order')
            ->getQuery()
            ->getResult();

        $questionByCategory = array();
        $subcategorys = array_unique(array_column($obQ, 'subcategory','subcategoryid'));

        foreach ($subcategorys as $key => $subcategory){
            $questions = array();
            foreach ($obQ as $question){
                if($question['subcategory'] === $subcategory){
                    array_push($questions, array(
                        'order' => $question['order'],
                        'question' => $question['question'],
                        'questionId' => $question['questionid'],
                        'observationAnswerId' => $question['observationAnswerId'],
                        'answer' => gettype($question['answer']) == 'boolean' ? (int)$question['answer'] : $question['answer'],
                        'comment' => $question['comment'],
                        'personId' => $question['personId'],
                        'dateRecord' => $question['dateRecord']
                    ));
                }
            }
            array_push($questionByCategory, array('categoryId' => $key, 'category' => $subcategory, 'questions' => $questions));
        }

        return $questionByCategory;
    }

    private function valObservationIdBy($findParameters){
        $em = $this->getDoctrine()->getManager();
        $observationAssigned = $em->getRepository('UNOEvaluacionesBundle:Observation')->findOneBy($findParameters);
        if ($observationAssigned) {
            if($observationAssigned->getFinish()){
                #ya esta finalizada por lo cual la puede ver
                $this->status = 200;
            }else {
                $this->status = 403;
                $this->message = "lo sentimos pero no puede acceder a esta página";
            }
        }else{
            #no le corresponde o no existe
            $this->status = 401;
            $this->message = "lo sentimos pero no es autorizado para acceder a esta página";
        }
    }

    private function getResultAspects($observationId){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $obAspects = $qb->select("OA.observationAspectsId, OA.inicioRelevante, OA.desarrolloRelevante, OA.cierreRelevante, OA.inicioMejorar, OA.desarrolloMejorar, OA.cierreMejorar")
            ->from('UNOEvaluacionesBundle:Observation', 'O')
            ->innerJoin('UNOEvaluacionesBundle:ObservationAspects','OA','WITH','O.observationId = OA.observationId')
            ->where('O.observationId = :observationId')
            ->setParameter('observationId', $observationId)
            ->getQuery()
            ->getResult();



        return array(
            'observationAspectsId' => $obAspects[0]['observationAspectsId'],
            'inicioRelevante' => $obAspects[0]['inicioRelevante'],
            'desarrolloRelevante' => $obAspects[0]['desarrolloRelevante'],
            'cierreRelevante' => $obAspects[0]['cierreRelevante'],
            'inicioMejorar' => $obAspects[0]['inicioMejorar'],
            'desarrolloMejorar' => $obAspects[0]['desarrolloMejorar'],
            'cierreMejorar' => $obAspects[0]['cierreMejorar']
        );
    }

    /**
     * @Route("/observacion/pdf/{observationId}")
     *
     * visiualiza la observacion
     */
    public function pdfAction(Request $request, $observationId)
    {
        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseURL = $this->container->get('router')->getContext()->getBaseUrl();
        $baseUrl = $scheme.$host.$baseURL;

        $session = $request->getSession();
        $session->start();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'inicio',
                'with' => 'none'
            ));
        }

        $this->setProfile($session->get('profileS'));

        if(in_array('COACH',$this->_profile)) {
            $this->valObservationIdBy(array('observationId' => $observationId, 'coachId' => $session->get('personIdS')) );
            $parameters['activities'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/activity/".$observationId, false),true);
            $parameters['info'] = $this->infoObservation("O.coachId = :coachId AND O.observationId = :observationId AND O.finish IS NOT NULL", array('coachId' => $session->get('personIdS'), 'observationId' => $observationId));
        }else{
            $this->valObservationIdBy(array('observationId' => $observationId, 'personId' => $session->get('personIdS')) );
            $parameters['info'] = $this->infoObservation("O.personId = :personId AND O.observationId = :observationId AND O.finish IS NOT NULL ", array('personId' => $session->get('personIdS'), 'observationId' => $observationId));
        }

        if($this->status == 200){
            $result = $this->getResQueryOQ($observationId);
            $aspects = $this->getResultAspects($observationId);

            $parameters['questionByCategory'] = $result;
            $parameters['observationId'] = $observationId;
            $parameters['disposition'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/disposition/".$observationId, false),true);
            $parameters['galleries'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/gallery/".$observationId, false),true);
            $parameters['aspects'] = $aspects;


            $urlImage = $scheme.$host.'/public/assets/images/observation/footer.png';
            $footer = '<!-- Footer -->
                <footer>
                    <div style="background-image: url('.$urlImage.'); background-size: 100% 100%; width: 100%; height: 94px;">
                    </div>
                </footer>
                <!-- END Footer -->';

            $snappy = $this->get('knp_snappy.pdf');
            $snappy->setOption('no-outline', true);
            $snappy->setOption('margin-top', 3);
            $snappy->setOption('margin-bottom', 28);
            $snappy->setOption('margin-right', 0);
            $snappy->setOption('margin-left', 0);
            //$snappy->setOption('footer-right', 'Pag. [page] de [toPage]');
            $snappy->setOption('outline-depth', 8);
            $snappy->setOption('orientation', 'Portrait');
            $snappy->setOption('footer-html', $footer);
	        $snappy->setOption('disable-javascript', true);
            $snappy->setOption('cache-dir', $this->get('kernel')->getRootDir(). '/../web/bundles/framework/wkhtmltox');
            
            $filename = 'Observación PDF';

            $html = $this->renderView('UNOEvaluacionesBundle:Observacion:pdf.html.twig', $parameters);

            return new Response(
                $snappy->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
                )
            );

        }else {
            return $this->render('UNOEvaluacionesBundle:Error:error.html.twig', array(
                'status' => $this->status,
                'message' => $this->message
            ));
        }


    }

    private function infoObservation($where, $setParameters){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $oByC = $qb->select("O.observationId, CONCAT(TRIM(P.name), ' ', TRIM(P.surname)) AS person, S.school, O.gradeId, O.groupId, Cp.nameProgram, O.start, O.finish, CONCAT(TRIM(P1.name), ' ', TRIM(P1.surname)) AS coach")
            ->from('UNOEvaluacionesBundle:Observation','O')
            ->innerJoin('UNOEvaluacionesBundle:Person','P','WITH','O.personId = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P1','WITH','O.coachId = P1.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','S','WITH','O.schoolId = S.schoolid')
            ->innerJoin('UNOEvaluacionesBundle:Cprogram','Cp','WITH','O.programId = Cp.programId')
            ->where($where)
            ->setParameters($setParameters)
            ->orderBy( 'O.observationId')
            ->getQuery()
            ->getResult();

        $nivel = array('K' => 'Kinder', 'P' => 'Primaria', 'S' => 'Secundaria', 'B' => 'Bachillerato');
        $observationsByCoach = array();
        foreach ($oByC as $row){
            $row['nivelCompleto'] = $nivel[strtoupper($row['gradeId'][1])];
            $row['grado'] = $row['gradeId'][0].'°';
            array_push($observationsByCoach, $row);
        }
        return $observationsByCoach;
    }
    /**
     * @Route("/observacion/pdfHtml/{observationId}")
     *
     * visiualiza la observacion
     */
    public function pdfHtmlAction(Request $request, $observationId)
    {
        #http://
        $scheme =  $this->container->get('router')->getContext()->getScheme().'://';
        #dev.ruta.unoi.com
        $host =  $this->container->get('router')->getContext()->getHost();
        #/app_dev.php
        $baseURL = $this->container->get('router')->getContext()->getBaseUrl();
        $baseUrl = $scheme.$host.$baseURL;

        $session = $request->getSession();
        $session->start();

        if(!Utils::isUserLoggedIn($session)){

            return $this->redirectToRoute('login',array(
                'redirect' => 'inicio',
                'with' => 'none'
            ));
        }

        $this->setProfile($session->get('profileS'));

        if(in_array('COACH',$this->_profile)) {
            $this->valObservationIdBy(array('observationId' => $observationId, 'coachId' => $session->get('personIdS')) );
            $parameters['activities'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/activity/".$observationId, false),true);
            $parameters['info'] = $this->infoObservation("O.coachId = :coachId AND O.finish IS NOT NULL", array('coachId' => $session->get('personIdS')));
        }else{
            $this->valObservationIdBy(array('observationId' => $observationId, 'personId' => $session->get('personIdS')) );
            $parameters['info'] = $this->infoObservation("O.personId = :personId AND O.finish IS NOT NULL ", array('personId' => $session->get('personIdS')));
        }

        if($this->status == 200){
            $result = $this->getResQueryOQ($observationId);
            $aspects = $this->getResultAspects($observationId);

            $parameters['questionByCategory'] = $result;
            $parameters['observationId'] = $observationId;
            $parameters['disposition'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/disposition/".$observationId, false),true);
            $parameters['galleries'] = json_decode(file_get_contents("$baseUrl/api/v0/observation/gallery/".$observationId, false),true);
            $parameters['aspects'] = $aspects;

            print_r($this->get('kernel')->getRootDir(). '/../web/bundles/framework/wkhtmltox');

            $urlImage = $scheme . $host . '/public/assets/images/observation/footer.png';
            $footer = '<!-- Footer -->
                <footer>
                    <div style="background-image: url(' . $urlImage . '); background-size: 100% 100%; width: 100%; height: 94px;">
                    </div>
                </footer>
                <!-- END Footer -->';

            return $this->render('UNOEvaluacionesBundle:Observacion:pdf.html.twig', $parameters);

        }
    }

}
