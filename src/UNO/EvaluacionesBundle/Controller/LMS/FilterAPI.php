<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 17/09/15
 * Time: 10:28 AM
 * Class LMS
 * @package UNO\EvaluacionesBundle\Controller\LMS
 */

namespace UNO\EvaluacionesBundle\Controller\LMS;

class FilterAPI{

    /**
     * Lista de Status
     * 1   - ok
     * 97  - Could not resolve host
     * 98  - Not valid JSON string
     * 99  - error de conexion
     * 100 - no existe en LMS
     * 101 - no es un perfil valido
     * 102 - no cuenta con algun periodo activo
     * 103 - no pertenece a Mexico
     *
     */


    static $status= 1;
    static $ok = FALSE;
    static $persona;
    static $person = 'person';
    static $schoolPeriodCode = 'schoolPeriodCode';
    static $profile = 'profile';
    static $school = 'school';
    static $country = 'country';
    static $schoolLevel = 'schoolLevel';

    static $profileG = FALSE;
    static $periodoG = FALSE;

    private $_person;
    private $_pass;
    private $_user;

    /**
     * FiltraAPI constructor.
     */
    public function __construct($json){
        $this->_person = $this->getDatPerson($json);
    }

    public function runFilter($user,$pass){
        $this->_pass = $pass;
        $this->_user = $user;
        return ($this->getPerson($this->_person));
    }

    private function getDatPerson($api){
        $dat = json_decode($api);
        return $dat->person;
    }

    private function getPerson($person){
        $Person = array();
        $schoolsArray = $this->getSchool($person->schools);
        if( is_array($schoolsArray) && !empty($schoolsArray) ){
            array_push(
                $Person,
                array(
                    'personId' => $person->personId,
                    'user' => $this->_user,
                    'pass' => $this->_pass,
                    'name' => $person->name,
                    'surname' => $person->surname,
                    'gender' => 'M',
                    'birthDay' => $person->birthDay,
                    'birthMonth' => $person->birthMonth,
                    'birthYear' => $person->birthYear,
                    'email' => trim($person->email,'--'),
                    'languageId' => $person->languageId,
                    'languageCode' => $person->languageCode,
                    'language' => $person->language,
                    'timeZone' => $person->timeZone,
                    'schools' => $schoolsArray
                )
            );
            return $Person[0];
        }else{
            return static::$status;
        }

    }

    /**
     * Función que obtiene las escuelas del array obtenido en la función Login
     *
     * @param $rows
     * @return array
     */
    private function getSchool($rows){
        $schoolArray = array();
        foreach ($rows as $schools) {
            foreach ($schools as $school) {
                foreach ($school as $value) {
                    if ($value->countryCode == 'MX') {
                        static::$status = 1;
                        $schoolPeriodsArray = $this->getSchoolPeriods($value->schoolPeriods);
                        if( is_array($schoolPeriodsArray) && !empty($schoolPeriodsArray) ){
                            array_push(
                                $schoolArray,
                                array(
                                    'schoolId' => $value->schoolId,
                                    'schoolCode' => $value->schoolCode,
                                    'school' => $value->school,
                                    'countryId' => $value->countryId,
                                    'countryCode' => $value->countryCode,
                                    'country' => $value->country,
                                    'languageId' => $value->languageId,
                                    'languageCode' => $value->languageCode,
                                    'language' => $value->language,
                                    'timeZone' => $value->timeZone,
                                    'schoolPeriods' => $schoolPeriodsArray
                                )
                            );
                        }
                    }else {
                        static::$status = 103;
                    }
                }
            }
        }
        return $schoolArray;
    }

    /**
     * Función que obtiene los periodos del array obtenido en la función Login
     *
     * @param $rows
     *
     * @return array
     */
    private function getSchoolPeriods($rows) {
        $schoolPeriodsArray = array();

        foreach ($rows as $schoolPeriods) {
            $periodoStatus = FALSE;
            foreach ($schoolPeriods as $schoolPeriod) {
                foreach ($schoolPeriod as $value) {
                    if ($value->schoolPeriodActive) {
                        $periodoStatus = TRUE;
                        static::$status = 1;
                        static::$periodoG = TRUE;
                        $schoolLevelsArray = $this->getSchoolLevels($value->schoolLevels);
                        if( is_array($schoolLevelsArray) && !empty($schoolLevelsArray) ){
                            array_push(
                                $schoolPeriodsArray,
                                array(
                                    'schoolPeriodId' => $value->schoolPeriodId,
                                    'schoolPeriodCode' => $value->schoolPeriodCode,
                                    'schoolPeriod' => $value->schoolPeriod,
                                    'schoolPeriodActive' => $value->schoolPeriodActive,
                                    'schoolLevels' => $schoolLevelsArray
                                )
                            );
                        }

                    }else {
                        if(!$periodoStatus  && !static::$periodoG )
                            static::$status = 102;
                    }
                }
            }
        }
        return $schoolPeriodsArray;
    }

    /**
     * Función que obtiene los niveles del array obtenido en la función Login
     *
     * @param $rows
     *
     * @return array
     */
    private function getSchoolLevels($rows) {
        $schoolLevelsArray = array();

        foreach ($rows as $schoolLevels) {
            foreach ($schoolLevels as $schoolLevel) {
                foreach ($schoolLevel as $value) {
                    $profileArray = $this->getProfiles($value->profiles);
                    if( is_array($profileArray) && !empty($profileArray) ){
                        array_push(
                            $schoolLevelsArray,
                            array(
                                'schoolLevelId' => $value->schoolLevelId,
                                'schoolLevelCode' => $value->schoolLevelCode,
                                'schoolLevel' => $value->schoolLevel,
                                'schoolLevelCompanyId' => $value->schoolLevelCompanyId,
                                'colegio_nivel_ciclo_id' => $value->colegio_nivel_ciclo_id,
                                'profiles' => $profileArray
                            )
                        );
                    }
                }
            }
        }
        return $schoolLevelsArray;
    }

    /**
     * Función que obtiene los perfiles del array obtenido en la función Login
     *
     * @param $rows
     *
     * @return array
     */
    private function getProfiles($rows) {
        $profileArray = array();
        foreach ($rows as $profiles) {
            $profileStatus = FALSE;
            foreach ($profiles as $profile) {
                foreach ($profile as $value) {
                    //id Familiar
                    $profileVal = array('18','19','20','21','26');
                    if (in_array($value->profileId, $profileVal)) {
                        $profileStatus = TRUE;
                        static::$profileG = TRUE;
                        static::$status = 1;
                        array_push($profileArray,
                            array('profileId' => $value->profileId,
                                'profileCode' => $value->profileCode,
                                'profile' => $value->profile
                            )
                        );
                    }else{
                        if(!$profileStatus && !static::$profileG)
                            static::$status = 101;
                    }
                }
            }
        }
        return $profileArray;
    }

    private static function creaArray($response) {
        $p = explode(',',$response['p']);
        if(!empty(static::$user)){
            $username = static::$user;
        }else{
            $username = $response[static::$person]['user'];
        }

        static::$persona = array(
            'username' => $username,
            'name' => $response[static::$person]['name']
        );
        $schools = static::escuelas($response[static::$person]['schools']);

        static::validaPerfil($schools);
    }

    private static function validaPerfil($schools) {
        $profileArray = array();
        foreach ($schools as $value) {
            $profileArray[$value['profileId']] = $value[static::$profile];
        }

        static::$persona[static::$profile] = $profileArray;
    }

}