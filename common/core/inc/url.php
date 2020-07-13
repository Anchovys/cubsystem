<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

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
     * Получает якорь из url (все, что идет после #)
     * @return string
     */
    public static function anchorHash()
    {
        $uri = self::currentUri(false,false);
        $uri_array =  explode('#', $uri);
        return count($uri_array) < 2 ? '' : $uri_array[1];
    }

    /**
     * Получить основной адрес Url
     * @param bool $anchorHashRemove  - удалить якорь
     * @param bool $queryStringRemove - удалить строку get параметров
     * @return string
     */
    public static function currentUri(bool $anchorHashRemove = TRUE, bool $queryStringRemove = TRUE)
    {
        if(!isset($_GET['m'])) return '';

        $url = CsSecurity::filter($_GET['m'], 'base');
        $url = str_replace(['.', '~', '\\'],  '_', $url);
        if($anchorHashRemove)
            $url = explode('#', $url)[0];
        if($queryStringRemove)
            $url = explode('?', $url)[0];

        return $url;
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
     * Проверяет строку с каким-то сегментом
     *
     * @param string $value - строка, которая должна быть равна сегменту
     * @param int $id - номер сегмента (по дефолту - 0)
     * @return array|bool|string
     */
    public static function segmentEquals(string $value, int $id = 0)
    {
        return self::segment($id) === CsSecurity::filter($value);
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

        $url = self::currentUri();
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