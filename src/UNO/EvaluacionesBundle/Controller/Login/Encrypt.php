<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 17/09/15
 * Time: 10:28 AM
 * Class LMS
 * @package UNO\EvaluacionesBundle\Controller\LMS
 */

namespace UNO\EvaluacionesBundle\Controller\Login;

/**
 * Clave privada para la encriptación de Passwords.
 */
DEFINE('ENCRYPTION_KEY', 'SomeRandomString');

class Encrypt{
    static $_key = '31CE5FE894ECF46134FC1AA365FF6F6F';


    /**
     * Devuelve una cadena encriptada.
     *
     * @param $pureString cadena original
     * @param string $encryptionKey clave opcional para la encriptación de la cadena
     *
     * @return string Devuelve la cadena encriptada
     */
    public static function encrypt($pureString) {
        # --- ENCRYPTION ---

        # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        # convert a string into a key
        # key is specified using hexadecimal
        //$key = pack('H*', $encryptionKey);
        $ciphertext_base64 = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, static::$_key, $pureString, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        //echo "<br/>".$ciphertext_base64;
        return $ciphertext_base64;
    }

    /**
     * Devuelve una cadena desencriptada
     *
     * @param $encryptedString cadena encriptada
     * @param string $encryptionKey clave opcional para la desencriptación de la cadena
     *
     * @return string Devuelve la cadena desencriptada
     */
    public static function decrypt($encryptedString) {
        # === WARNING ===

        # Resulting cipher text has no integrity or authenticity added
        # and is not protected against padding oracle attacks.

        # --- DECRYPTION ---

        # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        # convert a string into a key
        # key is specified using hexadecimal
        //$key = pack('H*', $encryptionKey);

        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, static::$_key, base64_decode($encryptedString), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

}