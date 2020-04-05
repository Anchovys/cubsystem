<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| url.php [rev 1.0], Назначение: хелпер для работы с Url
| -------------------------------------------------------------------------
|
| Хелпер может получать и всячески взаимодействовать с Url
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

function cs_abs_url($url = '')
{
    return CS__BASEURL . $url;
}

function cs_path_to_url($path, $absolute = TRUE)
{
    $path = $absolute ? str_replace(CS__BASEPATH, '', $path) : $path;
    $path = str_replace(['\\'],  '/', $path);
    //$path = str_replace(['.', '~'],  '_', $path);
    $path = CS__BASEURL . $path;

    return $path;
}

function cs_base_url()
{
    // http-address
    $base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    return $base_url;
}

function cs_full_url()
{
    // http-address
    $base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $full_url  = $base_url . $_SERVER['REQUEST_URI'];
    return $full_url;
}

function cs_get_segment($id = FALSE)
{
    if(!isset($_GET['m'])) return [];

    $url = cs_filter($_GET['m'], 'base');
    $url = str_replace(['.', '~', '\\'],  '_', $url);
    $url = explode('#', $url)[0];
    $url = explode('?', $url)[0];

    $segments = explode('/', $url);

    if($id === FALSE)
        return $segments;
    else return (!is_int($id) || !array_key_exists($id, $segments)) ? FALSE :
        $segments[$id];

}

/**
 * @param string $url
 * @param bool $absolute
 * @param string $header
 */
function cs_redir($url = '', $absolute = true, $header = '')
{
    $url = $absolute ? CS__BASEURL . $url : $url;
    $url = cs_filter($url, 'base');
    $url = strip_tags($url);
    $url = str_replace( array('%0d', '%0a'), '', $url );

    $header = cs_filter($header, 'int');

    if($header === 301)
        header('HTTP/1.1 301 Moved Permanently');
    elseif($header === 302)
        header('HTTP/1.1 302 Found');

    header("Refresh: 0; url={$url}");
    header("Location: {$url}");

    die();
}