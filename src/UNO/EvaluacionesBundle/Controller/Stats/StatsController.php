<?php

/**
 * Created by PhpStorm.
 * User: isra
 * Date: 16/10/15
 * Time: 10:17 AM
 */

namespace UNO\EvaluacionesBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UNO\EvaluacionesBundle\Entity\Personschool;
use UNO\EvaluacionesBundle\Entity\Optionxquestion;

class StatsController extends Controller{

    private $_profile = array();
    private $_schoolId = array();
    private $_jsonTotalResponsePie;
    private $_jsonTotalResponseColumn;
    private $_jsonTotalResponseDDColumn;
    private $_jsonListUser;
    private $_personId;
    private $_schoolIdFrm;
    private $_schoolIdPerson;
    private $_surveyAsig;
    private $_jsonListUserSinR = array();

    private $_resultsArray = array();
    private $_surveyResultsGral = array();

    private $_userList = array();

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
            $this->setPersonId($session);
            $this->setSchoolIdFrm($request);
            $this->setSchooIdPerson($session);
            $this->getResults();
            //$this->getUserResults();
            #vista para Admin
            if( in_array('SuperAdmin', $this->_profile) ){
                $this->getSchoolResponse();
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas Admin',
                    'jsonTotalResponsePie' => $this->_jsonTotalResponsePie,
                    'jsonTotalResponseColumn' => $this->_jsonTotalResponseColumn,
                    'userList' => $this->_userList,
                    'schoolId' => $this->_schoolId
                ));
            }else{
                #vista para director
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas',
                    'jsonTotalResponsePie' => $this->_jsonTotalResponsePie,
                    'jsonTotalResponseColumn' => $this->_jsonTotalResponseColumn,
                    'userList' => $this->_userList,
                ));
            }

        }else{
            return $this->redirect("/");
        }
    }

    private function setProfile($session){
        $profileJson = json_decode($session->get('profileS'));

        foreach($profileJson as $value){
            array_push($this->_profile, $value->profile);
        }
    }

    private function setPersonId($session){
        $this->_personId = $session->get('personIdS');
    }

    private function setSchooIdPerson($session){
        $schoolIdPerson = '';
        $_schoolIdPerson = json_decode($session->get('schoolIdS'));
        //$_schoolIdPerson = json_decode('[{"schoolid": 1500},{"schoolid": 1501}]');
        foreach($_schoolIdPerson as $value){
            $schoolIdPerson .= $value->schoolid. ', ';
        }
        $this->_schoolIdPerson = rtrim($schoolIdPerson, ', ');
    }

    private function setSchoolIdFrm($request){
        if(!empty($request->query->get('schoolId'))){
            $this->_schoolIdFrm = $request->query->get('schoolId');
        }else{
            $this->_schoolIdFrm = 0;
        }
    }

    private function getResults() {
        //obtencion de los datos generales para las estadisticas
        $this->getSurveyResultsGral();
        $this->getTotalResponse();
        //obtencion de los usuario
        $this->creaListUser();
        //$this->_surveyAsig = $this->getSurveyAsigUser();

        if( !empty($userResultsArray) ){
            $this->getTotalUserResponse($userResultsArray);
        }
    }

    private function getSurveyResultsGral() {
        $query = "SELECT P.personId, CONCAT(P.name, ' ', P.surname) as username, PS.schoolId, S.surveyId, S.title, count(A.answer) countAnswer, A.answer
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
                    ";
        if( in_array('SuperAdmin', $this->_profile) ){
            if($this->_schoolIdFrm != 0){
                $query .= "AND PS.schoolId in (".$this->_schoolIdFrm.")";
            }else{
                $query .= "AND PS.schoolId in (1253)";
            }
        }else{
            $query .= "AND PS.schoolId in (".$this->_schoolIdPerson.")";
        }

        $query .= " GROUP BY  P.personId, QS.Survey_surveyId, A.answer
                    ;";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        //$statement->bindValue('id', 123);
        $statement->execute();
        $this->_surveyResultsGral = $statement->fetchAll();

        //return $results;
    }

    private function getTotalResponse(){
        $si = 0;
        $no = 0;
        $nose = 0;
        $evalSiArray = array();
        $evalNoArray = array();
        $evalNoSeArray = array();
        //print_r($this->_surveyResultsGral);
        foreach($this->_surveyResultsGral as $value){
            switch ($value['answer']):
                case 'Sí':
                    $si += $value['countAnswer'];
                    array_push($evalSiArray, $value['title']);
                    break;
                case 'No':
                    $no += $value['countAnswer'];
                    array_push($evalNoArray, $value['title']);
                    break;
                default:
                    $nose += $value['countAnswer'];
                    array_push($evalNoSeArray, $value['title']);
            endswitch;
        }

        //grafica de pie
        $this->creaJsonToRePi($si, $no, $nose);
        //grafica de Columnas
        $this->creaJsonColumn($si, $no, $nose);
        //print_r($this->_surveyResultsGral);
        //print_r(array_count_values($this->_surveyResultsGral['personId']));
        /*
        $evalSi = $this->creaJsonDDColumn( $evalSiArray, "Sí" );
        $evalNo = $this->creaJsonDDColumn( $evalNoArray, "No" );
        $evalNoSe = $this->creaJsonDDColumn( $evalNoSeArray, "No sé" );

        $this->_jsonTotalResponseDDColumn = rtrim($evalSi.$evalNo.$evalNoSe,',');
        */
    }

    private function getPorcentaje($total, $parte, $redondear = 2) {
        return round($parte / $total * 100, $redondear);
    }

    private function creaJsonToRePi($si, $no, $nose){
        $total = $si + $no + $nose;
        $this->_jsonTotalResponsePie =
            "[
                {name:'Sí', y:".$this->getPorcentaje($total, $si, 2).", sliced:true, selected:true},
                {name:'No', y:".$this->getPorcentaje($total, $no, 2).", sliced:false, selected:false},
                {name:'No sé', y:".$this->getPorcentaje($total, $nose, 2).", sliced:false, selected:false}
            ]";
    }

    private function creaJsonColumn($si, $no, $nose){
        $this->_jsonTotalResponseColumn =
            "[
                {name:'Sí', y:$si},
                {name:'No', y:$no},
                {name:'No sé', y:$nose}
            ]";
    }

    private function creaJsonDDColumn($array, $id){
        $evalSi = '{
                    name: "'.$id.'",
                    id: "'.$id.'",
                    data: [';
        foreach (array_count_values($array) as $key => $value) {
            $evalSi .= "[\"$key\", $value],";
        }
        $evalSi = rtrim($evalSi, ',');
        $evalSi .= ']
                    },';

        return $evalSi;
    }

    private function getSchoolResponse() {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $this->_schoolId = $qb->select('PS.schoolid','Sc.school')
            ->from('UNOEvaluacionesBundle:Answer','A')
            ->innerJoin('UNOEvaluacionesBundle:Optionxquestion','OQ', 'WITH', 'A.optionxquestion = OQ.optionxquestionId')
            ->innerJoin('UNOEvaluacionesBundle:Questionxsurvey','QS', 'WITH', 'OQ.questionxsurvey = QS.questionxsurveyId')
            ->innerJoin('UNOEvaluacionesBundle:Question','Q', 'WITH', 'QS.questionQuestionid = Q.questionid')
            ->innerJoin('UNOEvaluacionesBundle:Subcategory','Sub', 'WITH', 'Q.subcategorySubcategoryid = Sub.subcategoryid')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'QS.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'A.personPersonid = PS.personid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->innerJoin('UNOEvaluacionesBundle:School','Sc', 'WITH', 'PS.schoolid = Sc.schoolid')
            ->groupBy('PS.schoolid')
            ->orderBy( 'PS.schoolid')
            ->getQuery()
            ->getResult();
    }

    private function creaListUser(){
        $_userEval = $this->getUserEval();
        $_surveyAsigUser = $this->getSurveyAsigUser();

        $userList = array();
        foreach ( $_surveyAsigUser as $totalUser ) {
            foreach ($_userEval as $userEval){
                if($totalUser['personId'] == $userEval['personId']){
                    array_push( $userList, array( 'personId' => $userEval['personId'], 'username' => $userEval['username'], 'progreso' => $userEval['realizadas'].'/'.$totalUser['asig'], 'avance' => $this->getPorcentaje($totalUser['asig'], $userEval['realizadas'])) );
                }else{
                    array_push( $userList, array( 'personId' => $totalUser['personId'], 'username' => $totalUser['username'], 'progreso' => '0'.'/'.$totalUser['asig'], 'avance' => $this->getPorcentaje($totalUser['asig'], 0)) );
                }
            }
        }

        $this->_userList = $userList;

    }

    private function getUserEval(){
        $query = "SELECT
                        P.personId,
                        CONCAT(P.name, ' ', P.surname) AS username,
                        COUNT(distinct(QS.Survey_surveyId)) as realizadas
                    FROM
                        Person P
                            INNER JOIN
                        Answer A ON P.personId = A.Person_personId
                            INNER JOIN
                        (SELECT DISTINCT
                            schoolId, personId
                        FROM
                            PersonSchool) PS ON P.personId = PS.personId
                            INNER JOIN
                        OptionXQuestion OQ ON A.OptionXQuestion_id = OQ.OptionXQuestion_id
                            INNER JOIN
                        QuestionXSurvey QS ON OQ.QuestionXSurvey_id = QS.QuestionXSurvey_id
                            INNER JOIN
                        Survey S ON QS.Survey_surveyId = S.surveyid
                    WHERE
                        P.personId !=1
                    ";

        if( in_array('SuperAdmin', $this->_profile) ){
            if($this->_schoolIdFrm != 0){
                $query .= "AND PS.schoolId in (".$this->_schoolIdFrm.")";
            }else{
                $query .= "AND PS.schoolId in (1253)";
            }
        }else{
            $query .= "AND PS.schoolId in (".$this->_schoolIdPerson.")";
        }

        $query .= " GROUP BY P.personId
                    ;";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->execute();
        $_userEval = $statement->fetchAll();

        return ($_userEval);
    }

    private function getSurveyAsigUser() {

        $query = "SELECT
                        P.personId,
                        CONCAT(P.name, ' ', P.surname) AS username,
                        COUNT(DISTINCT (SP.Survey_surveyId)) AS asig
                    FROM
                        SurveyXProfile SP
                            INNER JOIN
                        PersonSchool PS ON SP.Profile_profileId = PS.profileId
                            INNER JOIN
                        Person P ON PS.personId = P.personId
                    WHERE
 	                    P.personId !=1
                    ";

        if( in_array('SuperAdmin', $this->_profile) ){
            if($this->_schoolIdFrm != 0){
                $query .= "AND PS.schoolId in (".$this->_schoolIdFrm.")";
            }else{
                $query .= "AND PS.schoolId in (1253)";
            }
        }else{
            $query .= "AND PS.schoolId in (".$this->_schoolIdPerson.")";
        }

        $query .= " GROUP BY P.personId
                    ;";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        //$statement->bindValue('id', 123);
        $statement->execute();
        $_surveyAsigUser = $statement->fetchAll();

        return ($_surveyAsigUser);
    }

    private function getTotalUserResponse($userResultsArray){
        $userList = array();
        $tmp = array();
        $userRes = array();
        $i = 0;

        foreach ($userResultsArray as $keyUR => $valueUR) {

            foreach ($this->_surveyAsig as $keyAS => $valueSA) {
                if($valueUR['personid'] == $valueSA['personid']){
                    $userList[$i] = array(
                        'personid' => $valueUR['personid'],
                        'username' => $valueUR['username'],
                        'surveysAsig' => $valueSA['asig']
                    );
                    array_push($userRes, $valueUR['personid']);
                    $i++;
                }
            }

        }

        //print_r($userList);

        $user = $this->array_unique_multi($userList, 'personid');

        foreach ($user as $key1 => $value1) {
            foreach ($userResultsArray as $key2 => $value2) {
                if($value1['personid'] == $value2['personid']){
                    $rs = $this->resultPerson($value1['personid'], $value2['surveyid']);
                    array_push($tmp, array(
                            'surveyid' => $value2['surveyid'],
                            'title' => $value2['title'],
                            'si' => $rs['si'],
                            'no' => $rs['no'],
                            'nose' => $rs['nose'],
                            'eval' => $this->getEvalPerson($value1['personid'], $value2['surveyid'])
                        )
                    );
                    $user[$key1]['surveys'] = $tmp;
                }else{
                    $tmp = array();
                }
                $i++;
            }
        }
        //print_r($user);

        foreach($this->_surveyAsig as $valueS){
            if(!in_array($valueS['personid'],array_unique($userRes))){
                array_push($this->_jsonListUserSinR, $valueS['username']);
            }
        }
        $this->_jsonListUser = $user;
    }

    private function array_unique_multi($array, $key){
        //print_r($array);


        for ($i = 0; $i < count($array); $i++){
            $duplicate = null;
            for ($j=$i+1; $j<count($array); $j++){
                if (strcmp($array[$i][$key],$array[$j][$key]) === 0){
                    $duplicate = $j;
                    break;
                }
            }
            if (!is_null($duplicate))
                array_splice($array,$duplicate,1);
        }
        return $array;
    }

    private function resultPerson($personId, $surveyid){
        $si = 0;
        $no = 0;
        $nose = 0;
        $evalSiArray = array();
        $evalNoArray = array();
        $evalNoSeArray = array();

        foreach($this->_resultsArray as $value){
            if($personId == $value['personid'] && $surveyid == $value['surveyid'] ){
                switch ($value['answer']):
                    case 'Sí':
                        $si ++;
                        array_push($evalSiArray, $value['title']);
                        break;
                    case 'No':
                        $no ++;
                        array_push($evalNoArray, $value['title']);
                        break;
                    default:
                        $nose ++;
                        array_push($evalNoSeArray, $value['title']);
                endswitch;
            }
        }
        return array('si' => $si,'no' => $no,'nose' => $nose);
    }

    private function getEvalPerson($personId, $surveyid){

        $evalPersonArray = array();
        foreach($this->_resultsArray as $value){
            if($personId == $value['personid'] && $surveyid == $value['surveyid'] ){
                //$question = str_replace("'", "\\u0027",$value['question']);
                $question = htmlspecialchars($value['question'], ENT_QUOTES);
                array_push($evalPersonArray, array('order' => $value['order'], 'question' => $question, 'answer' => $value['answer']));
            }
        }
        return ($evalPersonArray);
    }



}