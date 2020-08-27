<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class CsSecurity
{
    /**
     * Заменяет всю кириллицу транслитом
     * @param string $text - текст для преобразования
     * @param bool $reverse - наоборот из транслита в кирилицу
     * @return string
     */
    static function transliterate(string $text, $reverse = FALSE)
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

        return !$reverse ? str_replace($cyr, $lat, $text) : str_replace($lat, $cyr, $text);
    }

    /* Проверка реферера */
    public static function checkReferer()
    {
        if (empty($_POST)) return;

        if (!isset($_SERVER['HTTP_REFERER']))
            die('Access denied!');

        $url = $ps = parse_url(self::filter($_SERVER['HTTP_REFERER'], 'xss'));
        $host = default_val_array($url, 'host');
        $port = default_val_array($url, 'host');

        if ($host && $port and $port != 80)
            $host .= ':' . $port;

        if ($host != $_SERVER['HTTP_HOST'])
            die('Access denied!');
    }

    /* Защита XSRF (CSRF) */
    public static function checkCSRFToken(string $token) : bool
    {
        $CS = CubSystem::getInstance();
        $real_token = $CS->info->getOption('security_CSRF-secure_token');

        if(empty_val($CS, $token))
            return FALSE;

        return hash_equals($real_token, $token);
    }

    /**
     * Очистить строку от XSS кода
     * @param $data string
     * @return string|string[]|null
     */
    static function xssClean(string $data)
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
     * @param string $mode - режим работы. можно комбинировать, через символ ';'
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
     *          transliterate   - преобразовать русский текст в латиницу
     *          sha512          - проверить является ли строка sha512 хешем в случае ошибки,
     *                            выведется пустой символ
     *
     *          base            - пресет. включает самые стандартные режимы
     *          username        - пресет. обрабатывает никнейм
     *          password        - пресет. обрабатывает пароль
     * @return string
     */
    static function filter($str, $mode = "base")
    {

        if ( !$str ) return $str;
        if ( !$mode ) return $str;

        if ( $str == null || count_chars( $str ) == 0 )
        {
            return $str;
        }

        $mode = trim( $mode );

        // обработка массива
        $mode = explode( ';', $mode );
        $mode = array_map( 'trim', $mode );
        $mode = array_unique( $mode );

        foreach ($mode as $rule)
        {
            // обозначим пресеты отдельным switch
            switch ($rule) {
                case 'base':
                    $str = self::filter( $str, 'trim;xss;strip_tags;special_chars' );
                    break;

                case 'username':
                    $str = self::filter( $str, 'base;string;spaces' );
                    if ( strlen( $str ) < 3 ) $str = '';
                    break;

                case 'password':
                    $str = self::filter( $str, 'base;string;spaces' );
                    if ( strlen( $str ) < 5 ) $str = '';
                    break;

                case 'special_string':
                    $str = self::filter( $str, 'base;string;spaces' );
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
                    if ( !is_int( $str ) || $str >= PHP_INT_MAX || $str <= PHP_INT_MIN )
                        $str = '';
                    break;

                case 'float':
                    $str = floatval( $str );
                    if ( !is_float($str ) || $str >= PHP_FLOAT_MAX || $str <= PHP_FLOAT_MIN )
                        $str = '';
                    break;

                case 'bool':
                    $str = filter_var( $str, FILTER_VALIDATE_BOOLEAN );
                    break;

                case 'email':
                    $str = preg_match( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str ) ? $str : '';
                    break;

                case 'ipv4':
                    $str = preg_replace( "/[^\d\.]+/", "", $str );
                    break;

                case 'domain':
                    $str = preg_replace( "/[^a-z0-9\-\.]+/i", "", $str );
                    break;

                case 'xss':
                    $str = self::xssClean( $str );
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
                    $str = preg_replace( '!/+!', '/', $str );
                    $str = str_replace(['../', './', '..'], '', $str );
                    break;

                case 'quotes':
                    $str = preg_replace( ['\'', '"', '`'], '', $str );
                    break;

                case 'transliterate':
                    $str = self::transliterate($str);
                    break;

                case 'multi_spaces':
                    $str = preg_replace( '!\s+!', ' ', $str );
                    break;

                case 'sha512':
                    $str = preg_match('/^[a-f0-9]{128}$/', $str ) ? $str : '';
                    break;
            }
        }

        return $str;
    }

    /**
     * Хеширует строку по алгоритму sha512, + можно засолить по секретному ключу
     * @param $str - строка, которую надо захешировать
     * @param bool|string $salted - использовать соль если True, то будет использован
     * стандартный секретный ключ. False - ничего, иначе то, что указано в переменной
     * @param string $algo - алгоритм хеширования
     * @return string
     */
    static function hash($str, bool $salted = TRUE, string $algo = 'sha512')
    {
        global $CS;

        $str = (string)$str;
        $str .= $salted === TRUE ? (string)$CS->config->getOption(['security', 'secret_key']) :
            ($salted !== FALSE ? (string)$salted : '');
        return hash($algo, $str);
    }

    /**
     * Сгенерировать псевдослучайную строку
     * @param int $length
     * @param bool $numbers
     * @param bool $upper
     * @param bool $special
     * @return string
     */
    static function rndStr($length = 10, $numbers = TRUE, $upper = TRUE, $special = FALSE)
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

    /**
     * Проверить то, что все указанные в args ключи
     * существуют в POST, иначе вывести false
     * @param array $args - какие ключи должны быть обязательно в POST
     * @return array|bool - Весь массив POST
     */
    public static function checkPost(array $args = [])
    {
        if(!$_POST)
             return FALSE;

        // встроили проверку рефера
        self::checkReferer();

        foreach ($args as $key => $field)
        {
            if (!isset($_POST[$field]))
            {
                return FALSE;
                break;
            }
        }

        return $_POST;
    }

    /**
     * Проверить то, что все указанные в args ключи
     * существуют в GET, иначе вывести false
     * @param array $args - какие ключи должны быть обязательно в GET
     * @return array|bool - Весь массив GET
     */
    public static function checkGet(array $args = [])
    {
        if(!$_GET)
            return FALSE;

        foreach ($args as $key => $field)
        {
            if (!isset($_GET[$field]))
            {
                return FALSE;
                break;
            }
        }

        return $_GET;
    }
}