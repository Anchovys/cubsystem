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

    // can use in main view
    $data = [
        'body'      => $CS->template->getBuffer('body'),
        'head'      => $CS->template->getBuffer('head'),
    ];

    // generate totally buffer, with put data in main view
    $CS->template->setBuffer('body', $CS->template->callbackLoad($data, 'main_view'), FALSE);
}
?>