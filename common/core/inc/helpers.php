<?php  defined('CS__BASEPATH') OR exit('No direct script access allowed');

function cs_load_helpers($path = CS_COMMONPATH . 'helpers' . _DS)
{
    global $CS;

    // totally array helpers (objects)
    $helpers = [];

    $helpers_for_load = is_array($h = $CS->config['helpers-priority']) ? $h : [];

    // allow to search helpers
    if($CS->config['helpers-search'] === TRUE || $path !== FALSE)
    {
        // get files in directory
        $files = cs_in_path_files($path, FALSE, ['php']);

        foreach($files as $value)
        {
            $value = pathinfo($value, PATHINFO_FILENAME);
            $value = str_replace('_helper', '', $value);
            if(!in_array($value, $helpers_for_load))
                array_push($helpers_for_load, $value);
        }
    }

    foreach($helpers_for_load as $helper)
    {
        if(array_key_exists($helper, $helpers))
            continue;

        $helper_suffix = '_helper';

        $helpers[$helper . $helper_suffix] = cs_load_one_helper($helper, $path, $helper_suffix);
    }

    return $helpers;
}

function cs_load_one_helper($helper, $path = CS_COMMONPATH . 'helpers' . _DS, $suffix = '_helper')
{
    global $CS;

    if(!file_exists($fn = $path . $helper . '.php')) // file not exists
        return NULL;

    require_once($fn); // connect the file

    $helper .= $suffix;

    // check mathes, class exists
    if(!preg_match("/^\w+$/i", $helper) || !class_exists($helper) ||
        array_key_exists($helper, $CS->autoload['helpers']))
        return NULL;

    return new $helper();
}
?>