<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/**
 * Return value or default
 * Возвращает значение, если оно есть,
 * либо возвращает дефолтное
 * @param $check
 * @param $default
 * @return null
 */
function default_val($check, $default = null)
{
    return is_null($check) || (is_bool($check) && !$check) ? $default : $check;
}

/**
 * Print any var
 * используется для отладки
 * @param $var
 * @param bool $html
 * @param bool $echo
 * @return false|string
 */
function pr($var, bool $html = TRUE, bool $echo = TRUE)
{
    if (!$echo) ob_start();
    else
        echo '<pre style="font-family: \'PT Mono\', sans-serif;">';

    if (is_bool($var)) echo $var ? 'TRUE' : 'FALSE';
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
 * @param $var
 * @param bool $html
 * @param bool $echo
 */
function _pr($var, bool $html = TRUE, bool $echo = TRUE)
{
    pr($var, $html, $echo);
    die();
}

/**
 * @param array $input
 * @return array
 */
function array_keys_recursive(array $input)
{
    $keys = array_keys($input);

    foreach ($input as $i)
    {
        if (is_array($i))
        {
            $keys = array_merge($keys, array_keys_recursive($i));
        }
    }

    return $keys;
}
