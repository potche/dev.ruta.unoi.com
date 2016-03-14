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

class FiltraAPI{

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
    static $user;
    static $pass;
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

    /**
     * FiltraAPI constructor.
     */
    public function __construct($json){
        $_person = $this->getDatPerson($json);
        print_r($this->getPerson($_person));
        echo "<hr/>";
        //print_r($_person->schools);
        $this->getSchool($_person->schools);
    }

    private function getDatPerson($api){
        $dat = json_decode($api);
        return $dat->person;
    }

    private function getPerson($person){
        $Person = array();
        $Person['personId'] = $person->personId;
        $Person['user'] = $person->user;
        $Person['name'] = $person->name;
        $Person['surname'] = $person->surname;
        $Person['gender'] = 'M';
        $Person['birthday'] = $person->birthDay;
        $Person['birthmonth'] = $person->birthMonth;
        $Person['birthyear'] = $person->birthYear;
        $Person['email'] = trim($person->email,'--');
        $Person['languageid'] = $person->languageId;
        $Person['languageCode'] = $person->languageCode;
        $Person['language'] = $person->language;
        $Person['timezone'] = $person->timeZone;
        return $Person;
    }

    /**
     * Función que obtiene las escuelas del array obtenido en la función Login
     *
     * @param $rows
     * @return array
     */
    public static function getSchool($row){

        foreach ($row as $schools) {
            foreach ($schools as $school) {
                foreach ($school as $value) {
                    print_r($value);
                    echo "<br/>---<br/>";
                }
            }
        }
        exit();
        $schoolsf = array();
        //schools
        foreach ($rows as $schools) {
            //school
            foreach ($schools as $school) {
                //data school
                foreach ($school as $value) {
                    if ($value['countryCode'] == 'MX') {
                        static::$status = 1;
                        $tmp = array(
//                        'schoolId' => $value['schoolId'],
//                        'schoolCode' => $value['schoolCode'],
                            'school' => $value[static::$school],
//                        'countryId' => $value['countryId'],
//                        'countryCode' => $value['countryCode'],
                            'country' => $value[static::$country]
                        );
                        $varios = static::periodos($value['schoolPeriods'], $tmp);
                        array_push($schoolsf, $varios);
                    } else {
                        static::$status = 103;
                    }
                }
            }
        }
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

    /**
     * Función que obtiene los periodos del array obtenido en la función Login
     *
     * @param array $rows
     * @return array
     */
    public static function getPeriodos($rows) {
        $periodos = array();
        foreach ($rows as $value) {
            $tmp = $value['schoolPeriod'];
            if (!in_array($tmp, $periodos)) {
                array_push($periodos, $tmp);
            }
        }
        return $periodos;
    }

    /**
     * Función que obtiene los Niveles del array obtenido en la función Login
     *
     * @param $rows
     * @return array
     */
    public static function getNiveles($rows) {
        $periodos = array();
        foreach ($rows as $value) {
            $tmp = $value[static::$schoolLevel];
            if (!in_array($tmp, $periodos)) {
                array_push($periodos, $tmp);
            }
        }
        return $periodos;
    }

    /**
     * Función que obtiene los Perfiles del array obtenido en la función Login
     *
     * @param $rows
     * @return array
     */
    public static function getPerfiles($rows) {
        $periodos = array();
        foreach ($rows as $value) {
            $tmp = $value[static::$profile];
            if (!in_array($tmp, $periodos)) {
                array_push($periodos, $tmp);
            }
        }
        return $periodos;
    }

    /**
     * Si dentro del array existe un key = "error", la conexión no fue valida
     *
     * @param array $result
     * @return boolean
     */
    public static function isValid($result) {
        $resp = false;
        if (array_key_exists('error', $result)) {
            $resp = false;
        } else {
            $resp = true;
        }
        return $resp;
    }

    /**
     * Función que obtiene las escuelas del array obtenido en la función Login
     *
     * @param $rows
     * @return array
     */
    public static function escuelas($rows) {
        $schoolsf = array();
        //schools
        foreach ($rows as $schools) {
            //school
            foreach ($schools as $school) {
                //data school
                foreach ($school as $value) {
                    if($value['countryCode'] == 'MX'){
                        static::$status = 1;
                        $tmp = array(
//                        'schoolId' => $value['schoolId'],
//                        'schoolCode' => $value['schoolCode'],
                            'school' => $value[static::$school],
//                        'countryId' => $value['countryId'],
//                        'countryCode' => $value['countryCode'],
                            'country' => $value[static::$country]
                        );
                        $varios = static::periodos($value['schoolPeriods'], $tmp);
                        array_push($schoolsf, $varios);
                    }else{
                        static::$status = 103;
                    }
                }
            }
        }

        $personas = array();
        foreach ($schoolsf as $nvl1) {
            foreach ($nvl1 as $nvl2) {
                foreach ($nvl2 as $nvl3) {
                    foreach ($nvl3 as $nvl4) {
                        array_push($personas, $nvl4);
                    }
                }
            }
        }
        return $personas;
    }

    /**
     * Función que obtiene los periodos del array obtenido en la función Login
     *
     * @param $rows
     * @param $personas
     *
     * @return array
     */
    public static function periodos($rows, $personas) {
        $schpl = array();

        foreach ($rows as $sp) {
            $periodo = FALSE;
            foreach ($sp['schoolPeriod'] as $value) {
                //solo periodos activos
                if ($value['schoolPeriodActive']) {
                    $periodo = TRUE;
                    static::$status = 1;
                    static::$periodoG = TRUE;
                    $tmp = array(
                        'schoolPeriodCode' => $value[static::$schoolPeriodCode]
                    );
                    $varios = static::niveles($value['schoolLevels'], array_merge($personas, $tmp));
                    array_push($schpl, $varios);
                }else{
                    if(!$periodo  && !static::$periodoG )
                        static::$status = 102;
                }
            }
        }
        return $schpl;
    }

    /**
     * Función que obtiene los niveles del array obtenido en la función Login
     *
     * @param $rows
     * @param $personas
     *
     * @return array
     */
    public static function niveles($rows, $personas) {
        $schpl = array();
        foreach ($rows as $sp) {
            foreach ($sp[static::$schoolLevel] as $value) {
                $tmp = array(
                    'schoolLevel' => $value[static::$schoolLevel]
                );
                $varios = static::perfiles($value['profiles'], array_merge($personas, $tmp));
                array_push($schpl, $varios);
            }
        }
        return $schpl;
    }

    /**
     * Función que obtiene los perfiles del array obtenido en la función Login
     *
     * @param $rows
     * @param $personas
     * @return array
     */
    public static function perfiles($rows, $personas) {
        $schpl = array();
        foreach ($rows as $sp) {
            foreach ($sp[static::$profile] as $value) {
                //id Familiar
                $tmp = array(
                    'profileId' => $value['profileId'],
                    static::$profile => $value[static::$profile]
                );
                array_push($schpl, array_merge($personas, $tmp));
            }
        }
        return $schpl;
    }

}