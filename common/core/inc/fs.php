<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
{
    if ($fp = @opendir($source_dir))
    {
        $filedata	= array();
        $new_depth	= $directory_depth - 1;
        $source_dir	= rtrim($source_dir, _DS)._DS;

        while (FALSE !== ($file = readdir($fp)))
        {
            // Remove '.', '..', and hidden files [optional]
            if (!trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) continue;

            if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir.$file))
                $filedata[$file] = directory_map($source_dir . $file . _DS, $new_depth, $hidden);
            else
                $filedata[] = $file;
            // $filedata[] = htmlentities($file, ENT_QUOTES, 'cp1251');
        }

        closedir($fp);
        return $filedata;
    }

    return FALSE;
}

function cs_in_path_files($path = '', $full_path = TRUE, $exts = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg'], $minus = TRUE)
{
    // if empty or not dir or empty dir
    if (!$path || !is_dir($path) || !$files = directory_map($path, true))
        return [];

    $all_files = []; // totalLy result

    foreach ($files as $file)
    {
        if (!is_file($path . $file)) // not a file
            continue;

        if (in_array(cs_file_ext($file), $exts)) // check a extension of file
        {
            if ($minus && strpos($file, '_') === 0) // check if starts with '_'
                continue;

            // add file with full path (if need)
            $all_files[] = $full_path ? $path . $file : $file;
        }
    }

    return $all_files;
}

function cs_file_ext($file)
{
    return strtolower(substr(strrchr($file, '.'), 1));
}
function cs_mk_htaccess()
{
    $htaccess = file_get_contents(CS_COMMONPATH . 'dist' . _DS . 'htaccess-distr.txt');
    $htaccess = str_replace('{path}', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', $htaccess);
    file_put_contents(CS__BASEPATH . '.htaccess', $htaccess) or die('Can`t make .htaccess file. Please, create this file or configure directory rules!');
}
?>