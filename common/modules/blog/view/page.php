<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

//////////
/// Логика отображения страницы
/// Из файла /blog/blog.php
//////////

global $CS;
$segments = cs_get_segment();

// берем сегмент
if($page_link = cs_get_segment(1))
{
    // выбираем страницу по сегменту
    $page = cs_page::getByLink($page_link);

    // страница есть
    if($page['count'] > 0)
    {
        $set_meta = TRUE;

        // генерируем буфер конкретной страницы
        $buffer = $this->_displayPages($page, 'blog/full-page_view', $set_meta);

        // ставим буфер
        $CS->template->setBuffer('body', $buffer, FALSE);

        // и мета-данные
        if($set_meta)
        {
            $CS->template->setMeta($page['result']->meta);
        }
    }
}