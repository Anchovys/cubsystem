<?php

function cs_absolute_url($url)
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

function cs_get_segment($id = FALSE)
{
    if(!isset($_GET['m'])) return [];

    $url = cs_filter($_GET['m'], 'base');
    $url = str_replace(['.', '~', '\\'],  '_', $url);
    $url = explode('#', $url)[0];
    $url = explode('?', $url)[0];

    $segments = explode('/', $url);

    return (!is_int($id) || !array_key_exists($id, $segments)) ? $segments :
        $segments[$id];
}

