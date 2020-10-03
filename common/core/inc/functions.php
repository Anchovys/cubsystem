<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
+ -------------------------------------------------------------------------
| functions.php [rev 1.0], Назначение: дополнительные функции
+ -------------------------------------------------------------------------
|
| Здесь описаны дополнительные функции системы.
|
*/

/**
 * Return value or default
 * Возвращает значение, если оно есть,
 * либо возвращает дефолтное
 * @param mixed $check
 * @param mixed $default
 * @return null
 */
function default_val($check, $default = NULL)
{
    return empty($check) ? $default : $check;
}

/**
 * Return value or default in array
 * Возвращает значение, если оно есть,
 * либо возвращает дефолтное
 * @param array $checkArray
 * @param mixed $checkKey
 * @param mixed $default
 * @return null
 */
function default_val_array(array $checkArray, $checkKey, $default = NULL)
{
    if(!array_key_exists($checkKey, $checkArray))
        return $default;

    return default_val($checkArray[$checkKey], $default);
}

/**
 * Аналогично стандартому empty,
 * но для нескольких значений
 * @return bool
 */
function empty_val() : bool
{
    $args = func_get_args();

    foreach ($args as $arg)
    {
        if(empty($arg)) return TRUE;
    }

    return FALSE;
}

/**
 * Print any var
 * используется для отладки
 * @param mixed $var
 * @param bool $html
 * @param bool $echo
 * @return bool|string
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
        return TRUE;
    }
}

/**
 * Аналогична pr, только завершающаяся die()
 * используется для отладки с помощью прерывания
 * @param mixed $var
 * @param bool $html
 * @param bool $echo
 */
function _pr($var, bool $html = TRUE, bool $echo = TRUE) : void
{
    pr($var, $html, $echo);
    die();
}

/**
 * Рекурсивный аналог функции PHP array_keys()
 * @param array $input
 * @return array
 */
function array_keys_recursive(array $input) : array
{
    $keys = array_keys($input);

    foreach ($input as $item)
    {
        if (is_array($item))
        {
            $keys = array_merge($keys, array_keys_recursive($item));
        }
    }

    return $keys;
}

/**
 * Рекурсивный аналог функции PHP array_values()
 * @param array $input
 * @return array
 */
function array_values_recursive(array $input) : array
{
    $values = [];

    foreach($input as $value)
    {
        if (is_array($value))
        {
            $values = array_merge($values, array_values_recursive($value));
            continue;
        }
        $values[] = $value;
    }
    return $values;
}