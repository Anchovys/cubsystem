<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| kernel.php [rev 1.0], Назначение: основные функции системы Cubsystem
| -------------------------------------------------------------------------
| В этом файле описаны основные функции, используемые системой
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

function cs_autoload_js($dir = CS__BASEPATH . 'js', $print_output = false)
{
    $files = cs_get_path_files($dir, true, ['js']);
    $output_html = '';
    

    foreach($files as $file)
    {
        $file = cs_path_to_url($file, TRUE);
        $output_html .= '<script src="' . $file . '"></script>'; 
    }

    if($print_output)
        print($output_html);

    return $output_html;
}

function cs_autoload_css($dir = CS__BASEPATH . 'css/')
{
    $files = cs_get_path_files($dir, true, ['css']);
    $output_html = '';
    
    foreach($files as $file)
    {
        $file = cs_path_to_url($file, true);
        $output_html .= '<link rel="stylesheet" href="' . $file . '">';
    }

    if($print_output)
        print($output_html);
    
    return $output_html;
}

function cs_get_segments()
{
    $url = trim($_GET['m']);
    $url = str_replace(['.', '~', '\\'],  '_', $url); 
    $url = explode('#', $url)[0];
    $url = explode('?', $url)[0]; 
    
    return explode('/', $url);
}

function cs_make_htaccess()
{
	$htaccess = file_get_contents(CS__KERNELPATH . 'dist' . _DS . 'htaccess-distr.txt');
	$htaccess = str_replace('{path}', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', $htaccess);
	file_put_contents(CS__BASEPATH . '.htaccess', $htaccess);
}

function cs_path_to_url($path, $absolute = TRUE) 
{
    $path = $absolute ? str_replace(CS__BASEPATH, '', $path) : $path;
    $path = str_replace(['\\'],  '/', $path); 
    //$path = str_replace(['.', '~'],  '_', $path); 
    $path = CS__BASEURL . $path;

    return $path;
}

function cs_file_ext($file)
{
	return strtolower(substr(strrchr($file, '.'), 1));
}

function cs_return_output($file, $__data = false)
{
    global $CS;
    ob_start();
    include $file;
    return ob_get_clean();
}

function cs_get_path_files($path = '', $full_path = TRUE, $exts = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg'], $minus = TRUE)
{
	// if empty or not dir or empty dir
    if (!$path || !is_dir($path) || !$files = directory_map($path, true))
        return [];

    $all_files = []; // totaly result

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

function cs_load_helpers($path = CS__KERNELPATH . 'helpers' . _DS) 
{
    // totally array helpers (objects)
    $helpers = [];

    // get files in directory
    $files = cs_get_path_files($path, false, ['php']);
    foreach($files as $file) 
    {
        if(!file_exists($fn = $path . $file)) // file not exsists
            continue;
        
        require_once($fn); // connect the file
            
        // make classname from filename
        $helper_name  = str_replace(  '.php', '',  $file);
        $helper_name  = str_replace(  '-',    '_', $helper_name);
        $helper_name .= '_helper';

        // check mathes, class exsists
        if(!preg_match("/^\w+$/i", $helper_name) || !class_exists($helper_name) ||
           array_key_exists($name, $helpers))
            continue;
        
        // add to array
        $helpers[$helper_name] = new $helper_name();
    }
    return $helpers;
}

function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
{
	if ($fp = @opendir($source_dir))
	{
		$filedata	= array();
		$new_depth	= $directory_depth - 1;
		$source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		while (FALSE !== ($file = readdir($fp)))
		{
			// Remove '.', '..', and hidden files [optional]
			if (!trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) continue;

			if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir.$file))
				$filedata[$file] = directory_map($source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden);
			else
				$filedata[] = $file;
				// $filedata[] = htmlentities($file, ENT_QUOTES, 'cp1251');
		}

		closedir($fp);
		return $filedata;
	}

	return FALSE;
}
?>