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

// минимизировать HTML код при отправке
$this->options['minify-html'] = TRUE;

// подгружать CSS автоматически,
// из папки /assets/css/autoload
$this->options['autoload_css'] = TRUE;

// вы можете сменить вышеуказанную папку CSS
// на любую свою, важно указывать абсолютный путь
$this->options['autoload_css_path'] = FALSE;

// подгружать JavaScript автоматически
// из папки /assets/js/autoload
$this->options['autoload_js'] = TRUE;

// вы можете сменить вышеуказанную папку JS
// на любую свою, важно указывать абсолютный путь
$this->options['autoload_js_path'] = FALSE;

$this->options['custom_meta']['favicon'] = "cs.jpg";

// главный callback файл,
// который содержит основную структуру страницы
// указывать нужно без .PHP
$this->options['main_view'] = 'main_view';

// использовать ли встроенный шаблонизатор,
// для всех компонентов шаблона
$this->options['tmpl_prepare'] = TRUE;

// ;; настройки пагинации
// вместо TRUE, можно указать FALSE, тогда пагинация не будет подключена
$this->options['pagination']['enable'] = TRUE;

// количество страниц на страницу пагинации
$this->options['pagination']['limit'] = 20;

// какой индекс использовать для опеределения страницы
// например /site/cat/category/next/3 (next - индекс)
$this->options['pagination']['index'] = 'next';

// сколько ссылок пагинации создавать, если 0,
// будут созданы только две ссылки - вперед / назад
$this->options['pagination']['max_links'] = 10;