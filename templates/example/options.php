<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| options.php, Назначение: настройки шаблона
| -------------------------------------------------------------------------
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

$template_settings = [];

// минимизировать HTML код при отправке
$template_settings['minify-html'] = TRUE;

// подгружать CSS автоматически,
// из папки /assets/css/autoload
$template_settings['autoload_css'] = TRUE;

// вы можете сменить вышеуказанную папку CSS
// на любую свою, важно указывать абсолютный путь
$template_settings['autoload_css_path'] = FALSE;

// подгружать JavaScript автоматически
// из папки /assets/js/autoload
$template_settings['autoload_js'] = TRUE;

// вы можете сменить вышеуказанную папку JS
// на любую свою, важно указывать абсолютный путь
$template_settings['autoload_js_path'] = FALSE;

// главный callback файл,
// который содержит основную структуру страницы
// указывать нужно без .PHP
$template_settings['main_view'] = 'main_view';

// использовать ли встроенный шаблонизатор,
// для всех компонентов шаблона
$template_settings['tmpl_prepare'] = TRUE;

// ;; настройки пагинации
// вместо [], можно указать FALSE, тогда пагинация не будет подключена
$template_settings['pagination'] = [];

// количество страниц на страницу пагинации
$template_settings['pagination']['limit'] = 10;

// какой индекс использовать для опеределения страницы
// например /site/cat/category/next/3 (next - индекс)
$template_settings['pagination']['index'] = 'next';

// сколько ссылок пагинации создавать, если 0,
// будут созданы только две ссылки - вперед / назад
$template_settings['pagination']['max_links'] = 10;
?>