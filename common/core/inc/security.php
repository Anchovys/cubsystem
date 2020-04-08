<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

function cs_hash_str($str, $salted = TRUE)
{
    global $CS;

    $str = (string)$str;
    $str .= $salted !== FALSE ? (string)$CS->config['secret_key'] : '';
    return hash('sha512', $str);
}

function cs_rnd_str($length = 10, $numbers = TRUE, $upper = TRUE, $special = FALSE)
{
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    if ($special == TRUE) $chars .= '$()[]{}#@!;:';
    if ($numbers == TRUE) $chars .= '0123456789';
    if ($upper   == TRUE) $chars .= 'ABCDEFGHIJKLMNOPRQSTUVWXYZ';

    $string = "";

    $len = strlen( $chars ) - 1;
    while (strlen( $string ) < $length) {
        $string .= $chars[mt_rand( 0, $len )];
    }

    return $string;
}
?>