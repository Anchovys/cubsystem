<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| index.php, Назначение: входной файл шаблона
| -------------------------------------------------------------------------
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

function onload_template()
{
    global $CS;

    // template
    $template = $CS->template;

    // get view file path
    $main_view_path = $template->getOption('main_view');

    // call main_view File
    $html = $template->callbackLoad($template, $main_view_path);

    // generate totally buffer, with put data in main view
    $template->setBuffer('body', $html, FALSE);
}