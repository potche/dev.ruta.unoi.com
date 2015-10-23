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

    private $_resultsArray = array();
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
            $this->getUserResults();
            #vista para Admin
            if( in_array('SuperAdmin', $this->_profile) ){
                $this->getSchoolResponse();
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas Admin',
                    'jsonTotalResponsePie' => $this->_jsonTotalResponsePie,
                    'jsonTotalResponseColumn' => $this->_jsonTotalResponseColumn,
                    'jsonTotalResponseDDColumn' => $this->_jsonTotalResponseDDColumn,
                    'jsonListUser' => $this->_jsonListUser,
                    'schoolId' => $this->_schoolId
                ));
            }else{
                #vista para director
                return $this->render('UNOEvaluacionesBundle:Stats:index.html.twig', array(
                    'title' => 'Estadisticas',
                    'jsonTotalResponsePie' => $this->_jsonTotalResponsePie,
                    'jsonTotalResponseColumn' => $this->_jsonTotalResponseColumn,
                    'jsonTotalResponseDDColumn' => $this->_jsonTotalResponseDDColumn,
                    'jsonListUser' => $this->_jsonListUser
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
        $this->_resultsArray = $this->getSurveyResults();

        if( !empty($this->_resultsArray) ){
            $this->getTotalResponse();
        }
    }

    private function getSurveyResults() {
        $query = "SELECT P.personid, CONCAT(P.name, ' ', P.surname) as username, PS.schoolid, Sc.school, S.surveyid, S.title, QS.order, Q.question, Sub.subcategory, A.answer, A.comment
                                  FROM UNOEvaluacionesBundle:Answer A
                                  INNER JOIN UNOEvaluacionesBundle:Optionxquestion OQ WITH A.optionxquestion = OQ.optionxquestionId
                                  INNER JOIN UNOEvaluacionesBundle:Questionxsurvey QS WITH OQ.questionxsurvey = QS.questionxsurveyId
                                  INNER JOIN UNOEvaluacionesBundle:Question Q WITH QS.questionQuestionid = Q.questionid
                                  INNER JOIN UNOEvaluacionesBundle:Subcategory Sub WITH Q.subcategorySubcategoryid = Sub.subcategoryid
                                  INNER JOIN UNOEvaluacionesBundle:Survey S WITH QS.surveySurveyid = S.surveyid
                                  INNER JOIN UNOEvaluacionesBundle:Personschool PS WITH A.personPersonid = PS.personid
                                  INNER JOIN UNOEvaluacionesBundle:Person P WITH PS.personid = P.personid
                                  INNER JOIN UNOEvaluacionesBundle:School Sc WITH PS.schoolid = Sc.schoolid
                                  ";

        $em = $this->getDoctrine()->getManager();
        if( in_array('SuperAdmin', $this->_profile) ){
            if($this->_schoolIdFrm != 0){
                $query .= "WHERE PS.schoolid = ".$this->_schoolIdFrm;
            }
        }else{
            $query .= "WHERE PS.schoolid in (".$this->_schoolIdPerson.")";
        }
        $query .= "GROUP BY A.personPersonid, PS.schoolid, S.surveyid, QS.order
                   ORDER BY A.personPersonid, PS.schoolid, S.surveyid, QS.order";
        $q = $em->createQuery($query);
        $results = $q->getResult();
        return $results;
    }

    private function getTotalResponse(){
        $si = 0;
        $no = 0;
        $nose = 0;
        $evalSiArray = array();
        $evalNoArray = array();
        $evalNoSeArray = array();

        foreach($this->_resultsArray as $value){
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

        //grafica de pie
        $this->creaJsonToRePi($si, $no, $nose);
        //grafica de Columnas
        $this->creaJsonColumn($si, $no, $nose);
        $evalSi = $this->creaJsonDDColumn( $evalSiArray, "Sí" );
        $evalNo = $this->creaJsonDDColumn( $evalNoArray, "No" );
        $evalNoSe = $this->creaJsonDDColumn( $evalNoSeArray, "No sé" );

        $this->_jsonTotalResponseDDColumn = rtrim($evalSi.$evalNo.$evalNoSe,',');
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

    private function getUserResults() {
        $userResultsArray = $this->getUserEval();

        if( !empty($userResultsArray) ){
            $this->getTotalUserResponse($userResultsArray);
        }
    }

    private function getUserEval(){
        $query = "SELECT P.personid, CONCAT(P.name, ' ', P.surname) as username, S.surveyid, S.title
                                  FROM UNOEvaluacionesBundle:Answer A
                                  INNER JOIN UNOEvaluacionesBundle:Optionxquestion OQ WITH A.optionxquestion = OQ.optionxquestionId
                                  INNER JOIN UNOEvaluacionesBundle:Questionxsurvey QS WITH OQ.questionxsurvey = QS.questionxsurveyId
                                  INNER JOIN UNOEvaluacionesBundle:Question Q WITH QS.questionQuestionid = Q.questionid
                                  INNER JOIN UNOEvaluacionesBundle:Subcategory Sub WITH Q.subcategorySubcategoryid = Sub.subcategoryid
                                  INNER JOIN UNOEvaluacionesBundle:Survey S WITH QS.surveySurveyid = S.surveyid
                                  INNER JOIN UNOEvaluacionesBundle:Personschool PS WITH A.personPersonid = PS.personid
                                  INNER JOIN UNOEvaluacionesBundle:Person P WITH PS.personid = P.personid
                                  INNER JOIN UNOEvaluacionesBundle:School Sc WITH PS.schoolid = Sc.schoolid
                                  ";

        $em = $this->getDoctrine()->getManager();
        if( in_array('SuperAdmin', $this->_profile) ){
            if($this->_schoolIdFrm != 0){
                $query .= "WHERE PS.schoolid = ".$this->_schoolIdFrm;
            }
        }else{
            $query .= "WHERE PS.schoolid in (".$this->_schoolIdPerson.")";
        }
        $query .= "GROUP BY A.personPersonid, PS.schoolid, S.surveyid
                   ORDER BY A.personPersonid, PS.schoolid, S.surveyid";
        $q = $em->createQuery($query);
        $results = $q->getResult();
        return $results;
    }

    private function getTotalUserResponse($userResultsArray){
        $userList = array();
        $tmp = array();
        $i = 0;

        foreach ($userResultsArray as $key => $value) {
            $userList[$i] = array(
                'personid' => $value['personid'],
                'username' => $value['username'],
                'surveysAsig' => $this->getSurveyAsigUser($value['personid'])
            );
            $i++;
        }

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
                            'nose' => $rs['nose']
                        )
                    );
                    $user[$key1]['surveys'] = $tmp;
                }else{
                    $tmp = array();
                }
                $i++;
            }



        }
        print_r($user);

        $this->_jsonListUser = $user;
    }

    private function array_unique_multi($array, $key){
        for ($i = 0; $i < count($array); $i++){
            $duplicate = null;
            for ($j = $i+1; $j < count($array); $j++){
                if (strcmp($array[$j][$key],$array[$j][$key]) === 0){
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

    private function getSurveyAsigUser($personId) {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $rs =  $qb->select('count(distinct(S.surveyid)) as asig ')
            ->from('UNOEvaluacionesBundle:Surveyxprofile','SP')
            ->innerJoin('UNOEvaluacionesBundle:Survey','S', 'WITH', 'SP.surveySurveyid = S.surveyid')
            ->innerJoin('UNOEvaluacionesBundle:Personschool','PS', 'WITH', 'SP.profileProfileid = PS.profileid')
            ->innerJoin('UNOEvaluacionesBundle:Person','P', 'WITH', 'PS.personid = P.personid')
            ->where('PS.personid = '.$personId)
            ->getQuery()
            ->getResult();

        return $rs[0]['asig'];
    }

}