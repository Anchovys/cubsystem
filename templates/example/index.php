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

global $CS;

$data =
    [
        'content'=> $this->html_buffer,
        'meta' => $this->generateMeta($this->meta_data)
    ];

$this->html_buffer = $this->callbackLoad($data , 'main_view');

if($this->settings['minify-html'] && $minify = $CS->gc('html_minify_helper', 'helpers'))
    $this->html_buffer = $minify->minify($this->html_buffer);
?>