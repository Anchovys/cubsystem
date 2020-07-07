<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */
/*
 * функция для отладки кода pr($любая_переменная)
 * $html == true - преобразование спецсимволов в HTML, иначе отдается как есть
 * $echo == true - сразу вывод в браузер, иначе возврат по return
 |
 	@
	@   Cubsystem CMS, (с) 2019
	@   Author: Anchovy
	@   GitHub: //github.com/Anchovys/cubsystem
	@
 |
 */

/**
 * Print any var
 * используется для отладки
 */
function pr($var, $html = true, $echo = true)
{
    if (!$echo)
        ob_start();
    else
        echo '<pre>';

    if (is_bool($var))
    {
        if ($var)
            echo 'TRUE';
        else
            echo 'FALSE';
    }
    else
    {
        if (is_scalar($var))
        {
            if (!$html)
            {
                echo $var;
            }
            else
            {
                $var = str_replace('<br />', "<br>", $var);
                $var = str_replace('<br>', "<br>\n", $var);
                $var = str_replace('</p>', "</p>\n", $var);
                $var = str_replace('<ul>', "\n<ul>", $var);
                $var = str_replace('<li>', "\n<li>", $var);
                $var = htmlspecialchars($var);
                $var = wordwrap($var, 300);

                echo $var;
            }
        }
        else
        {
            if (!$html)
                print_r($var);
            else
                echo htmlspecialchars(print_r($var, true));
        }
    }

    if (!$echo)
    {
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }
    else
    {
        echo '</pre>';
    }
}

/**
 * Аналогична pr, только завершающаяся die()
 * используется для отладки с помощью прерывания
 */
function _pr($var, $html = true, $echo = true)
{
    pr($var, $html, $echo);
    die();
}