<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

//////////
/// Логика отображения главной
/// Из файла /blog/blog.php
//////////


global $CS;


// отображать в обратном порядке
$reverse    = TRUE;

// какие поля нужно выбрать
$needle     = [
    'cats', 'id', 'title',
    'short_text', 'link',
    'tag', 'author', 'views',
    'comments'
];

// получим пагинацию у текущего шаблона
$pagination = $CS->template->getPagination();

// выборка страниц
$result = cs_page::getListAll($pagination, $needle, TRUE);

// соберем буфер
$buffer = $this->_displayPages($result, 'blog/short-page_view');

// и установим
$CS->template->setBuffer('body', $buffer, FALSE);

// и поставим мета данные
$CS->template->setMeta([
    'title' => "Home Page",
    'description' => "Welcome to our home page!"
]);