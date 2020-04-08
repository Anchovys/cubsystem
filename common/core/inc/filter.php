<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
* Наборы функций для фильтрации разных значений
|
?   OLD
?   CODE
@
@   Cubsystem CMS, (с) 2019
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
|
*/

function cs_translite_text($text = FALSE)
{
    $cyr = [
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
        'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
        'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я',
        'š','č','đ','č','ć','ž','ñ','Š','Č','Đ','Č','Ć','Ž','Ñ'

    ];
    $lat = [
        'a','b','v','g','d','ie','io','zh','z','i','ai','k','l','m','n','o','p',
        'r','s','t','u','f','h','ts','ch','sh','sht','','i','','e','yu','ja',
        'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
        'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya',
        's','c','d','c','c','z','n','S','C','D','C','C','Z','N'
    ];

    return str_replace($cyr, $lat, $text);
}

function xss_clean($data)
{
    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    // we are done...
    return $data;
}

/**
 * Зачистка строки, числа, булевой и т.д.
 * @param $str - переменная string для очистки
 * @param $mode - режим работы. можно комбинировать, через символ ';'
 *          spaces          - очистка пробелов во всей строке
 *          trim            - очистка пробелов в начале и конце
 *          string          - очистка для строки
 *          int, integer    - очистка для целочисленного. в случае ошибки, выведется пустой символ
 *          float           - очистка для числа с плавающей точкой
 *          bool            - очистка для булевой переменной
 *          email           - очистка для email адреса. в случае ошибки, выведется пустой символ
 *          ipv4            - очистка для ip адреса (v4)
 *          domain          - очистка для доменного имени
 *          xss             - очистка от XSS
 *          sl              - экранирование кавычек слешами
 *          strip_tags      - удалить все тэги
 *          special_chars   - преобразовать в html-спецсимволы
 *          not_url         - удалить все признаки url
 *          to_lower        - преобразовать к нижнему регистру
 *          to_upper        - преобразовать к верхнему регистру
 *          path            - очищает все 'опасные' символы в пути
 *          quotes          - очищает кавычки из строки
 *          special_string  - безопасная строка, в которой используется только
 *                            ОДНО СЛОВО из букв латинского алфавата с нижним регистром
 *          multi_spaces    - заменяет многочисленные пробелы одним символом
 *       transliterate text - преобразовать русский текст в латиницу
 *          md5             - проверить является ли строка md5 хешем в случае ошибки, выведется пустой символ
 *
 *          base            - пресет. включает самые стандартные режимы
 *          username        - пресет. обрабатывает никнейм
 *          password        - пресет. обрабатывает пароль
 * @return Var
 */
function cs_filter ($str, $mode = "base")
{

    if ( !$str ) return $str;
    if ( !$mode ) return $str;

    if ( $str == null || count_chars( $str ) == 0 )
    {
        return $str;
    }

    $mode = trim( $mode );

    //обработка массива
    $mode = explode( ';', $mode );
    $mode = array_map( 'trim', $mode );
    $mode = array_unique( $mode );

    foreach ($mode as $rule)
    {
        //обозначим пресеты отдельным switch
        switch ($rule) {
            case 'base':
                $str = cs_filter( $str, 'trim;xss;strip_tags;special_chars' );
                break;

            case 'username':
                $str = cs_filter( $str, 'base;string;spaces' );
                if ( strlen( $str ) < 3 ) $str = '';
                break;

            case 'password':
                $str = cs_filter( $str, 'base;string;spaces' );
                if ( strlen( $str ) < 5 ) $str = '';
                break;

            case 'special_string':
                $str = cs_filter( $str, 'base;string;spaces' );
                $str = preg_replace( '/[^a-zA-Z0-9-]/', '', $str );
                $str = substr($str, 0, 64);
                break;
        }

        switch ($rule) {

            case 'spaces':
                $str = preg_replace( '!\s+!', ' ', $str );
                $str = preg_replace( '/\s+/', '-', $str );
                break;

            case 'trim':
                $str = trim( $str );
                break;

            case 'string':
                $str = preg_replace( "/[\'\"\`\>\<\{\\\}\%]+/i", "", $str );
                break;

            case 'integer':
            case 'int':
                $str = intval( $str );
                if ( !is_int( $str ) || $str > 4294967295 )
                    $str = '';
                break;

            case 'float':
                $str = floatval( $str );
                break;

            case 'bool':
                $str = filter_var( $str, FILTER_VALIDATE_BOOLEAN );
                break;

            case 'email':
                $str = (!preg_match( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str )) ? '' : $str;
                break;

            case 'ipv4':
                $str = preg_replace( "/[^\d\.]+/", "", $str );
                break;

            case 'domain':
                $str = preg_replace( "/[^a-z0-9\-\.]+/i", "", $str );
                break;

            case 'xss':
                $str = xss_clean( $str );
                break;

            case 'sl':
                $str = addslashes( $str );
                break;

            case 'strip_tags':
                $str = strip_tags( $str );
                break;

            case 'special_chars':
                $str = htmlspecialchars( $str );
                break;

            case 'not_url':
                $str = str_replace( ['http://', 'https://', '\\', '|', '/', '?', '%', '*', '`', '<', '>', '#', '&amp;', '^', '&', '(', ')', '+', '$'], '', $str );
                break;

            case 'to_lower':
                $str = mb_strtolower( $str );
                break;

            case 'to_upper':
                $str = mb_strtoupper( $str );
                break;

            case 'path':
                $str = preg_replace( ['\\', '../', './', '..'], '', $str );
                break;

            case 'quotes':
                $str = preg_replace( ['\'', '"', '`'], '', $str );
                break;

            case 'transliterate':
                $str = cs_translite_text($str);
                break;

            case 'multi_spaces':
                $str = preg_replace( '!\s+!', ' ', $str );
                break;

            case 'sha512':
                $str = (!preg_match('/^[a-f0-9]{128}$/', $str )) ? '' : $str;
                break;

                /*
            case 'chars':
                $str = preg_replace("/[^\w]+/i", "", $str);
            break;
            case 'nums':
                $str = preg_replace("/[^\d]+/", "", $str);
            break;
            */
        }
    }

    return $str;
}