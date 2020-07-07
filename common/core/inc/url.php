<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class CsUrl
{
    /**
     * Преобразует url в абсолютный в зависимости от домена
     * @param string $url
     * @return string
     */
    public static function absUrl(string $url = '')
    {
        return self::baseUrl() . $url;
    }

    /**
     * Преобразует путь в Url адрес
     * @param $path - путь
     * @param bool $absolute - абсолютный путь или нет
     * @return string|string[]
     */
    public static function pathToUrl(string $path, bool $absolute = TRUE)
    {
        $path = $absolute ? str_replace(CS__BASEPATH, '', $path) : $path;
        $path = str_replace(['\\'],  '/', $path);
        // $path = str_replace(['.', '~'],  '_', $path);
        $path = self::baseUrl() . $path;

        return $path;
    }

    /**
     * Получить основной адрес Url
     * @return string
     */
    public static function baseUrl()
    {
        // http-address
        $base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
        return $base_url;
    }

    /**
     * Получить полный адрес Url
     * @return string
     */
    public static function fullUrl()
    {
        // http-address
        $base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        return $base_url . $_SERVER['REQUEST_URI'];
    }

    /**
     * Вернет сегмент с номером, либо весь массив сегментов
     *
     * @param int $id - номер сегмента (не обязательно)
     * @return array|bool|string
     */
    public static function segment(?int $id = NULL)
    {
        if(!isset($_GET['m'])) return [];

        $url = CsSecurity::filter($_GET['m'], 'base');
        $url = str_replace(['.', '~', '\\'],  '_', $url);
        $url = explode('#', $url)[0];
        $url = explode('?', $url)[0];

        $segments = explode('/', $url);

        if($id === NULL) return $segments;
        else return (!is_int($id) || !array_key_exists($id, $segments)) ? FALSE :
            $segments[$id];
    }

    /**
     * @param string $url
     * @param bool $absolute
     * @param string $header
     */
    public static function redir($url = '', $absolute = true, $header = '')
    {
        $url = $absolute ? self::baseUrl() . $url : $url;
        $url = CsSecurity::filter($url, 'base');
        $url = strip_tags($url);
        $url = str_replace( array('%0d', '%0a'), '', $url );

        $header = CsSecurity::filter($header, 'int');

        if($header === 301)
            header('HTTP/1.1 301 Moved Permanently');
        elseif($header === 302)
            header('HTTP/1.1 302 Found');

        header("Refresh: 0; url={$url}");
        header("Location: {$url}");

        die();
    }
}