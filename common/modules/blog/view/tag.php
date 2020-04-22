<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| tag.php, Назначение: Логика отображения страниц с тегом
| Из файла /blog/blog.php
| -------------------------------------------------------------------------
| В этом файле описана базовая функциональность для блога
| работа со статьями, базой данных
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

global $CS;

// получим пагинацию у текущего шаблона
$pagination = $CS->template->getPagination();
$segments   = cs_get_segment();

// какие поля нужно выбрать
$needle     = [
    'cats', 'id', 'title',
    'short_text', 'link',
    'tag', 'author', 'views',
    'comments', 'date'
];

// отображать в обратном порядке
$reverse    = TRUE;

// берем сегмент
if($page_link = cs_get_segment(1)) {

    $page = cs_page::getListByTag($page_tag = $segments[1], $pagination, $needle, TRUE);

    // вывод xss ленты (если есть подходящий адрес)
    if(function_exists('xss_feed_check') && xss_feed_check())
        die(xss_feed_display($result));

    $buffer = $this->_displayPages($page, 'blog/short-page_view', FALSE);


    $CS->template->setBuffer('body', $buffer, FALSE);


    $CS->template->setMeta([
        'title' => "Tag: {$page_tag}",
        'description' => "Here you can see all page with tag: {$page_tag}"
    ]);
}