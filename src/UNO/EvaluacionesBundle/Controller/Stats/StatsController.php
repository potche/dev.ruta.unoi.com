<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 16/10/15
 * Time: 10:28 AM
 * Class Login
 * @package UNO\EvaluacionesBundle\Controller\Login
 */


namespace UNO\EvaluacionesBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

/**
 * Class StatsController
 * @package UNO\EvaluacionesBundle\Controller\Stats
 */
class StatsController extends Controller{

    private $_profile = array();
    private $_schoolId = array();
    private $_personId;
    private $_schoolIdFrm;
    private $_schoolIdPerson;

    private $_surveyResultsGral = array();
    private $_jsonTotalResponsePie = '';
    private $_jsonTotalResponseColumn = '';
    private $_userList = array();
    private $_nameSchool = 'General';

    /**
     * @Route("/estadisticas")
     *
     * Muestra las estadisticas
     */
    public function indexAction(Request $request){

        $session = $request->getSession();
        $session->start();
        if ($session->get('logged_in')) {
            $this->setProfile($session);
            if (array_intersect(array('SuperAdmin','Director','COACH'), $this->_profile)) {
                $this->setPersonId($session);
                $this->setSchoolIdFrm($request);
                $this->setSchooIdPerson($session);
                $this->getResults();
                #vista para Admin
                if (array_intersect(array('SuperAdmin','COACH'), $this->_profile)) {
                    $this->getSchoolResponse();
                    return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                        'nameSchool' => $this->_nameSchool,
                        'jsonTotalResponsePie' => $this->_jsonTotalResponsePie,
                        'jsonTotalResponseColumn' => $this->_jsonTotalResponseColumn,
                        'userList' => $this->_userList,
                        'schoolId' => $this->_schoolId
                    ));
                } else {
                    #vista para director
                    return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                        'nameSchool' => $this->_nameSchool,
                        'jsonTotalResponsePie' => $this->_jsonTotalResponsePie,
                        'jsonTotalResponseColumn' => $this->_jsonTotalResponseColumn,
                        'userList' => $this->_userList,
                    ));
                }
            }return $this->redirect("/inicio");
        }else{
            return $this->redirect("/");
        }
    }

    /**
     * @param $session
     *
     * inicializa el atributo $this->_profile con los perfiles del usuario de session
     */
    private function setProfile($session){
        $profileJson = json_decode($session->get('profileS'));

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    /**
     * @param $session
     *
     * inicializa el atributo $this->_personId con el id del usuario de session
     */
    private function setPersonId($session){
        $this->_personId = $session->get('personIdS');
    }

    /**
     * @param $session
     *
     * filtra la escuela que puede ver el usuario
     */
    private function setSchooIdPerson($session){
        $schoolIdPerson = '';
        $_schoolIdPerson = json_decode($session->get('schoolIdS'));
        if (!array_intersect(array('SuperAdmin','COACH'), $this->_profile)) {
            foreach($_schoolIdPerson as $value){
                $schoolIdPerson .= $value->schoolid. ', ';
                $this->_nameSchool = $value->school;
            }
        }

        $this->_schoolIdPerson = rtrim($schoolIdPerson, ', ');
    }

    /**
     * @param $request
     *
     * cacha el colegio filtrado y enviado por metdo post (Admin)
     */
    private function setSchoolIdFrm($request){
        $idPost = $request->request->get('schooIdFrm');
        if(!empty($idPost)){
            $id = explode('-',$idPost);
            $this->_schoolIdFrm = $id[0];
            $this->_nameSchool = $id[1];
        }else{
            $this->_schoolIdFrm = 0;
            $this->_nameSchool = 'General';
        }
    }

    /**
     * este metodo invoca a otros metodos con el fin de separar las acciones
     */
    private function getResults(){
        //obtencion de los datos generales para las estadisticas
        $this->getSurveyResultsGral();
        if (!empty($this->_surveyResultsGral)) {
            $this->getTotalResponse();
            //obtencion de los usuario
            $and = "PS.schoolid != ''";
            if( array_intersect(array('SuperAdmin','COACH'), $this->_profile) ){
                if($this->_schoolIdFrm != 0){
                    $and = "PS.schoolid in (".$this->_schoolIdFrm.")";
                    $this->creaListUser($and);
                }else{
                    $this->_userList = "";
                }
            }else{
                $and = "PS.schoolid in (".$this->_schoolIdPerson.")";
                $this->creaListUser($and);
            }

        }
    }

    /**
     * obtiene toda la informacion de las evaluaciones realizadas por colegio o general
     */
    private function getSurveyResultsGral() {
        $query = "SELECT count(A.answer) countAnswer, A.answer
                    FROM

                        Person P
                            INNER JOIN
                        Answer A ON P.personId = A.Person_personId
                            INNER JOIN
                        (SELECT DISTINCT schoolId, personId FROM PersonSchool) PS ON P.personId = PS.personId
                            INNER JOIN
                        OptionXQuestion OQ ON A.OptionXQuestion_id = OQ.OptionXQuestion_id
                            INNER JOIN
                        QuestionXSurvey QS ON OQ.QuestionXSurvey_id = QS.QuestionXSurvey_id
                            INNER JOIN
                        Survey S ON QS.Survey_surveyId = S.surveyid
                        WHERE P.personId != 1
                        AND S.active = 1
                    ";
        if( array_intersect(array('SuperAdmin','COACH'), $this->_profile) ){
            if($this->_schoolIdFrm != 0){
                $query .= "AND PS.schoolId in (".$this->_schoolIdFrm.")";
            }
        }else{
            $query .= "AND PS.schoolId in (".$this->_schoolIdPerson.")";
        }

        $query .= " GROUP BY A.answer
                    ;";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->execute();
        $this->_surveyResultsGral = $statement->fetchAll();
    }

    /**
     *calcula el nuemero de respuestas por si, no y no se
     */
    private function getTotalResponse(){
        $si = 0;
        $no = 0;
        $nose = 0;

        foreach($this->_surveyResultsGral as $value){
            switch ($value['answer']):
                default:
                    $nose = $value['countAnswer'];
                    break;
                case 'Sí':
                    $si = $value['countAnswer'];
                    break;
                case 'No':
                    $no = $value['countAnswer'];
                    break;
            endswitch;
        }

        //grafica de pie
        $this->creaJsonToRePi($si, $no, $nose);
        //grafica de Columnas
        $this->creaJsonColumn($si, $no, $nose);
    }

    /**
     * @param $total
     * @param $parte
     * @param int $redondear
     * @return float
     *
     * Calcula el porcentaje
     */
    private function getPorcentaje($total, $parte, $redondear = 2) {
        return round($parte / $total * 100, $redondear);
    }

    /**
     * @param $si
     * @param $no
     * @param $nose
     *
     * Crea el Json para la grafica de pie
     */
    private function creaJsonToRePi($si, $no, $nose){
        $total = $si + $no + $nose;
        if($total != 0){
            $this->_jsonTotalResponsePie =
                "[
                {name:'Sí', y:".$this->getPorcentaje($total, $si, 2).", sliced:true, selected:true},
                {name:'No', y:".$this->getPorcentaje($total, $no, 2).", sliced:false, selected:false},
                {name:'No sé', y:".$this->getPorcentaje($total, $nose, 2).", sliced:false, selected:false}
            ]";
        }else{
            $this->_jsonTotalResponsePie = '';
        }

    }

    /**
     * @param $si
     * @param $no
     * @param $nose
     *
     * Crea el Json para la grafica de Columna
     */
    private function creaJsonColumn($si, $no, $nose){
        $this->_jsonTotalResponseColumn =
            "[
                {name:'Sí', y:$si},
                {name:'No', y:$no},
                {name:'No sé', y:$nose}
            ]";
    }

    /**
     * obtiene una lista con loas escuelas para poder filtrar (unicamente para Admin)
     * la almacena en el atributo $this->_schoolId
     */
    private function getSchoolResponse() {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_schoolId = $qb->select("CONCAT(trim(PS.schoolid),'-',trim(S.school)) as school")
            ->from('UNOEvaluacionesBundle:Personschool','PS')
            ->innerJoin('UNOEvaluacionesBundle:School','S', 'WITH', 'PS.schoolid = S.schoolid')
            ->groupBy('PS.schoolid')
            ->orderBy( 'PS.schoolid')
            ->getQuery()
            ->getResult();

        $jsonSchoolId = '';
        foreach($_schoolId as $value){
            $jsonSchoolId .= '"'.$value['school'].'",';
        }
        $jsonSchoolId = trim($jsonSchoolId, ',');
        $this->_schoolId = $jsonSchoolId;
    }

    /**
     *Crea la lista de usuario con su progreso, avance y evaluaciones relizadas
     */
    private function creaListUser($and){
        $_userEval = $this->getUserEval($and);
        $evalUsers = array_unique(array_column($_userEval, 'personid'));

        $userList = array();
        foreach($evalUsers as $valUSers) {
            $si = 0;
            $no = 0;
            $personid = "";
            $username = "";
            foreach ($_userEval as $totalUser) {
                if($valUSers == $totalUser['personid']){
                    $personid = $totalUser['personid'];
                    $username = $totalUser['username'];
                    switch ($totalUser['finished']):
                        case 'si':
                            $si++;
                            break;
                        case 'no':
                            $no++;
                            break;
                    endswitch;
                }
            }
            $totalUser = $si + $no;
            array_push( $userList,
                array(
                    'personId' => $personid,
                    'username' => $username,
                    'progreso' => $si.'/'.$totalUser,
                    'avance' => $this->getPorcentaje($totalUser, $si)
                )
            );
        }
        $this->_userList = $userList;
    }

    /**
     * obtiene el numero de evaluaciones que ha realizado cada usuario
     * @return mixed
     */
    private function getUserEval($and){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $_userEval = $qb->select("PS.personid, CONCAT(P.name,' ', P.surname) AS username,S.surveyid")
            ->addSelect("CASE WHEN ( A.actioncode = '004') THEN 'si' ELSE 'no'  AS finished")
            ->from('UNOEvaluacionesBundle:Surveyxprofile ','SP')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'PS.profileid = SP.profileProfileid AND PS.schoollevelid = SP.schoollevelid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'S.surveyid = SP.surveySurveyid')
            ->leftJoin('UNOEvaluacionesBundle:Log','L', 'WITH', 'L.surveySurveyid = S.surveyid AND L.personPersonid = PS.personid')
            ->leftJoin('UNOEvaluacionesBundle:Action','A', 'WITH', 'A.idaction = L.actionaction')
            ->leftJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->leftJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->leftJoin('UNOEvaluacionesBundle:Option','O', 'WITH', 'O.optionid = OQ.optionOptionid')
            ->leftJoin('UNOEvaluacionesBundle:Answer','Ans', 'WITH', 'Ans.optionxquestion = OQ.optionxquestionId AND Ans.personPersonid = PS.personid')
            ->where('S.active = 1')
            ->andWhere('PS.personid > 1')
            ->andWhere('S.closingdate >= CURRENT_DATE()')
            ->andWhere($and)
            ->groupBy('PS.personid, S.surveyid, finished')
            ->orderBy( 'PS.schoolid')
            ->addOrderBy('finished')
            ->getQuery()
            ->getResult();

        return ($_userEval);
    }

    /**
     * array_unique para un array multiple
     * @param $array
     * @param $key
     * @return mixed
     */
    private function array_unique_multi($array, $key){

        for ($i = 0; $i < count($array); $i++){
            $duplicate = null;
            for ($j=$i+1; $j<count($array); $j++){
                if (strcmp($array[$i][$key],$array[$j][$key]) === 0){
                    $duplicate = $j;
                    break;
                }
            }
            if (!is_null($duplicate)){
                array_splice($array,$duplicate,1);
            }
        }
        return $array;
    }

}