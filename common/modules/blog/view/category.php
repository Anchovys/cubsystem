<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

//////////
/// Логика отображения категорий
/// Из файла /blog/blog.php
//////////

global $CS;

// получим пагинацию у текущего шаблона
$pagination = $CS->template->getPagination();

$segments = cs_get_segment();
$start = 1; // с какого индекса начинают идти категории (1)
$end   = count($segments) - 1; // и заканчивают

// если есть пагинация, то кончаться могут другим индексом
// тогда конец категорий - это начало пагинаций
if($pagination)
{
    // указано
    if($start_id = $pagination->getSegmentStartId())
    {
        // записываем
        $end = $start_id;
    }
}

// вырежем список категорий
$cat_list = array_slice($segments, $start, $end);

// какие поля нужно выбрать
$needle     = [
    'cats', 'id', 'title',
    'short_text', 'link',
    'tag', 'author', 'views',
    'comments'
];

// отображать в обратном порядке
$reverse    = TRUE;

if(count($cat_list) === 0)
    return;
//if(count($cat_list) === 1)
//{

// по какой категории выбрали
$category = cs_cat::getByLink($cat_list[0], ['id']);
$result = cs_page::getByCategoryId($category->id, $pagination, $needle, $reverse);
//}
//else
//{
//    $ids = [];
//    foreach ($cat_list as $cat_link)
//    {
//        $category = cs_cat::getByLink($cat_link, ['id']);
//        $ids[] = $category->id;
//    }
//    $page = cs_page::getByCategoryIds($ids);
//}

// соберем буфер
$buffer = $this->_displayPages($result, 'blog/short-page_view');

// и установим
$CS->template->setBuffer('body', $buffer, FALSE);
$CS->template->setMeta([
    'title' => "Cat: {$category->name}",
    'description' => "Here you can see all page with cat: {$category->name}"
]);