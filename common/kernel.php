<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| kernel.php [rev 1.2], Назначение: основные функции системы Cubsystem
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

function cs_autoload_js($dir = CS__BASEPATH . 'js', $print_output = FALSE)
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

function cs_autoload_css($dir = CS__BASEPATH . 'css/', $print_output = FALSE)
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
    if(!isset($_GET['m'])) return [];
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
	file_put_contents(CS__BASEPATH . '.htaccess', $htaccess) or die('Can`t make .htaccess file. Please, create this file or configure directory rules!');
}

function cs_path_to_url($path, $absolute = TRUE) 
{
    $path = $absolute ? str_replace(CS__BASEPATH, '', $path) : $path;
    $path = str_replace(['\\'],  '/', $path); 
    //$path = str_replace(['.', '~'],  '_', $path); 
    $path = CS__BASEURL . $path;

    return $path;
}

function cs_absolute_url($url)
{
    return CS__BASEURL . $url;
}

function cs_redir($url = '', $absolute = true, $header = '')
{
    $url = $absolute ? CS__BASEURL . $url : $url;
    $url = strip_tags($url);
    $url = str_replace( array('%0d', '%0a'), '', $url );

    //to-do xss

    $header = intval($header);

    if($header === 301)
        header('HTTP/1.1 301 Moved Permanently');
    elseif($header === 302)
        header('HTTP/1.1 302 Found');

    header("Refresh: 0; url={$url}");
    header("Location: {$url}");

    die();
}

function cs_file_ext($file)
{
	return strtolower(substr(strrchr($file, '.'), 1));
}

function cs_return_output($file, $__data = false)
{
    global $CS;

    ob_start();

    if(file_exists($file))
        include($file);

    return ob_get_clean();
}

function cs_hash_str($str, $salted = TRUE)
{
    global $CS;

    $str = (string)$str;
    $str .= (string)$CS->config['secret_key'];
    return md5($str);
}

function cs_get_random_str($length = 10, $numbers = TRUE, $upper = TRUE, $special = FALSE)
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

function cs_get_path_files($path = '', $full_path = TRUE, $exts = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg'], $minus = TRUE)
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

function cs_load_helpers($path = CS__KERNELPATH . 'helpers' . _DS) 
{
    global $CS;

    // totally array helpers (objects)
    $helpers = [];

    $helpers_for_load = is_array($h = $CS->config['helpers-priority']) ? $h : [];

    // get files in directory
    $files = cs_get_path_files($path, FALSE, ['php']);

    foreach($files as $value)
    {
        $value = pathinfo($value, PATHINFO_FILENAME);
        $value = str_replace('_helper', '', $value);
        if(!in_array($value, $helpers_for_load))
            array_push($helpers_for_load, $value);
    }

    foreach($helpers_for_load as $helper)
    {
        if(!file_exists($fn = $path . $helper . '.php')) // file not exists
            continue;

        require_once($fn); // connect the file

        $helper .= '_helper';

        // check mathes, class exists
        if(!preg_match("/^\w+$/i", $helper) || !class_exists($helper) ||
           array_key_exists($helper, $helpers))
            continue;
        
        // add to array
        $helpers[$helper] = new $helper();
    }

    return $helpers;
}

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
?>