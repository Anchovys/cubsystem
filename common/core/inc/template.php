<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/**
 * @param string $dir
 * @param bool $print_output
 * @return string
 */
function csAutoloadJs($dir = CS__BASEPATH . 'js', $print_output = FALSE)
{
    $files = csGetPathFiles($dir, true, ['js']);
    $output_html = '';


    foreach($files as $file)
    {
        $file = csPathToUrl($file, TRUE);
        $output_html .= '<script src="' . $file . '"></script>';
    }

    if($print_output)
        print($output_html);

    return $output_html;
}

/**
 * @param string $dir
 * @param bool $print_output
 * @return string
 */
function csAutoloadCss($dir = CS__BASEPATH . 'css/', $print_output = FALSE)
{
    $files = csGetPathFiles($dir, true, ['css']);
    $output_html = '';

    foreach($files as $file)
    {
        $file = csPathToUrl($file, true);
        $output_html .= '<link rel="stylesheet" href="' . $file . '">';
    }

    if($print_output)
        print($output_html);

    return $output_html;
}

/**
 * Функция для подключения файла
 * @param $file - полный путь к файлу
 * @param string $__data - любая переменная, доступная изнутри файла
 * @param bool $custom  -  любая функция,
 *                         через которую может пропускаться буфер
 * @return false|string
 */
function csReturnOutput($file, $__data = '', $custom = FALSE)
{
    global $CS;

    ob_start();

    if(file_exists($file))
    {
        if($custom == FALSE)
        {
            include($file);
        }
        else {

            /* ***********   ***********   ***********   ***********   ***********
                кастомный случай, например для реализации шаблонизаторов,
                или других функций, обрабатывающих исходные коды шаблонов
            *  ***********   ***********   ***********   ***********   ********** */

            // получим код из файла
            $code = file_get_contents($file);

            // если в custom, например, функция
            if(is_callable($custom))
            {
                // вызов функции
                $res = $custom($code);

                // вернула string, заменим целиком
                if(is_string($res))
                    $code = $res;
            }

            // выполним код
            eval($code);

        }
    }

    return ob_get_clean();
}
?>