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

class LMS{

    /**
     * Lista de Etatus
     * 1   - ok
     * 98  - Not valid JSON string
     * 99  - error de conexion
     * 100 - no existe en LMS
     * 101 - no es un perfil valido
     * 102 - no cuenta con algun periodo activo
     * 103 - no pertenece a Mexico
     *
     */

    /**
     * @var resource $conn The client connection instance to use.
     * @access private
     */
    private $_conn = null;
    /**
     * @var resource $_failure error.
     * @access private
     */
    private $_failure = null;
    /**
     * @var resource $_api REST.
     * @access private
     */
    private $_api = null;
    /**
     * @var null
     * @access private
     */
    private $_user = null;
    /**
     * @var null
     * @access private
     */
    private $_password = null;
    

    /*
     * hay dos formas de enviar para metros al API: User y Password, IP y Token
     */
    public function __construct(){
        if (!is_resource($this->_conn)) {
            if (!$this->getConnection())
                $this->_failure = '99';
        }
    }

    public function getDataXUserPass($user, $pass, $request_url = ""){
        if(is_null($this->_failure)){
            $params = $this->params($user, $pass);

            curl_setopt($this->_conn, CURLOPT_URL, $request_url);  // URL to post to
            curl_setopt($this->_conn, CURLOPT_RETURNTRANSFER, 1 );   // return into a variable
            $headers = array('Content-type: application/json');
            curl_setopt($this->_conn, CURLOPT_HTTPHEADER, $headers ); // custom headers
            curl_setopt($this->_conn, CURLOPT_HEADER, false );     // return into a variable
            curl_setopt($this->_conn, CURLOPT_POST, true);     // POST
            $postBody = (!empty($params))? json_encode($params) : "{}";
            curl_setopt($this->_conn, CURLOPT_POSTFIELDS,  $postBody);

            $result = curl_exec( $this->_conn );
            if ($result === false) {
                $this->_failure = 'Curl Error: ' . curl_error($this->_conn);
            }else{
                $this->getJSON($result);
            }

            $this->closeConnection();

            return $this->getData();
        }else{
            return $this->_failure;
        }
    }

    /**
     * @param $result
     */
    protected function getJSON($result){
        $responseCode = curl_getinfo($this->_conn, CURLINFO_HTTP_CODE);

        if (!self::successfulHttpResponse($responseCode)){
            $this->_failure = 'Error ['.$responseCode.']: ' . $result;
        }else{
            $parsedResult = json_decode($result,true);

            if (json_last_error()===JSON_ERROR_NONE) {
                if(!$this->isValid($parsedResult)){
                    $this->_failure = '100';
                }else{
                    $this->_api = $parsedResult;
                }
            } else {
                $error = json_last_error_msg();
                $this->_failure = "99 ($error)";
            }
        }
    }

    /**
     * @return resource|string
     */
    protected function getData() {
        if(is_null($this->_failure)){
            return $this->_api;
        }else{
            return $this->_failure;
        }
    }

    /**
     * Get the connection
     * @return boolean
     */
    protected function getConnection(){
        $this->_conn = curl_init();
        return is_resource($this->_conn);
    }

    /**
     * Close the connection
     */
    protected function closeConnection(){
        curl_close($this->_conn);
    }

    /**
     * successful HttpResponse
     */
    private static function successfulHttpResponse($code){
        if ($code >= 200 and $code < 300){
            return true;
        }
        return false;
    }

    /**
     * Return an error
     * @param string $msg Error message
     * @return array Result
     */
    protected function failure($msg){
        return array(
            'success' => false,
            'message' => $msg
        );
    }

    /**
     * @param $user
     */
    protected function setUser($user){
        $this->_user = $user;
    }

    /**
     * @return $this->_user
     */
    protected function getUser(){
        return $this->_user;
    }

    /**
     * @param $password
     */
    protected function setPassword($password){
        $this->_password = $password;
    }

    /**
     * @return $this->_password
     */
    protected function getPassword(){
        return $this->_password;
    }

    /**
     * @param $user
     * @param $pass
     * @return array
     */
    protected function params($user, $pass){
        $this->setUser($user);
        $this->setPassword($pass);

        return $params = array('user' => $this->getUser(), 'password' => $this->getPassword());
    }

    /**
     * Si dentro del array existe un key = "error", los parametros no son correctos...
     *
     * @param array $result
     * @return boolean
     */
    public static function isValid($result) {
        if (array_key_exists('error', $result)) {
            $rs = false;
        } else {
            $rs = true;
        }
        return $rs;
    }

}