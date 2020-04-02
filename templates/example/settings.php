<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| settings.php, Назначение: настройки шаблона
| -------------------------------------------------------------------------
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

$template_settings =
[
    // минимизировать HTML код при отправке
    'minify-html'           => TRUE,

    // подгружать CSS автоматически,
    // из папки /assets/css/autoload
    'autoload_css'          => TRUE,

    // вы можете сменить вышеуказанную папку CSS
    // на любую свою, важно указывать абсолютный путь
    'autoload_css_path'     => FALSE,

    // подгружать JavaScript автоматически
    // из папки /assets/js/autoload
    'autoload_js'           => TRUE,

    // вы можете сменить вышеуказанную папку JS
    // на любую свою, важно указывать абсолютный путь
    'autoload_js_path'      => FALSE,

    // главный callback файл,
    // который содержит основную структуру страницы
    // указывать нужно без .PHP
    'main_view'             => 'main_view',

    // использовать ли встроенный шаблонизатор,
    // для всех компонентов шаблона
    'tmpl_prepare'          => TRUE,
];

?>