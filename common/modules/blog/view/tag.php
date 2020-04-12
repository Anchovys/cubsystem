<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

//////////
/// Логика отображения тегов
/// Из файла /blog/blog.php
//////////

global $CS;

// получим пагинацию у текущего шаблона
$pagination = $CS->template->getPagination();
$segments   = cs_get_segment();

// какие поля нужно выбрать
$needle     = [
    'cats', 'id', 'title',
    'short_text', 'link',
    'tag', 'author', 'views',
    'comments'
];

// отображать в обратном порядке
$reverse    = TRUE;

// берем сегмент
if($page_link = cs_get_segment(1)) {

    $page = cs_page::getListByTag($page_tag = $segments[1], $pagination, $needle, TRUE);

    $buffer = $this->_displayPages($page, 'blog/short-page_view', FALSE);


    $CS->template->setBuffer('body', $buffer, FALSE);


    $CS->template->setMeta([
        'title' => "Tag: {$page_tag}",
        'description' => "Here you can see all page with tag: {$page_tag}"
    ]);
}