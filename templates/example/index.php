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

    // generate HEAD section (meta, js scripts, css, etc .. )
    $CS->template->generateMeta($CS->template->meta_data);


    // can use in main view
    $data = [
        'body'      => $CS->template->body_buffer,
        'head'      => $CS->template->head_buffer
    ];

    // generate totally buffer, with put data in main view
    $CS->template->body_buffer = $CS->template->callbackLoad($data, 'main_view');
}
?>