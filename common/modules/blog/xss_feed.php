<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| xss_feed.php, Назначение: вывод xss ленты
| -------------------------------------------------------------------------
| В этом файле описаны функции для вывода xss ленты
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

// проверить на то, что в урле нужно вызвать ленту xss
function xss_feed_check()
{
    $segments = cs_get_segment();
    $last_segment = $segments[count($segments)-1];

    return $last_segment === 'feed';
}

// вывести строку ленты по данным
function xss_feed_display($data_pages)
{
    if(!isset($data_pages['count']) || $data_pages['count'] === 0)
    {
        return false;
    }

    $total_pages = $data_pages['result'];

    $content = '<?xml version="1.0" encoding="utf-8"?>
                <rss version="2.0">
                <channel>';

    foreach($total_pages as $page)
    {
        $title = $page->meta['title'];
        $description = $page->meta['description'];
        $link = cs_abs_url('');
        $pubDate = $page->date;

        $content .= "<item>
            <title>{$title}</title>
            <link>{$link}</link>
            <description>{$description}</description>
            <pubDate>{$pubDate}</pubDate>
            <language>en-ru</language>
            <generator>Cubsystem cms</generator>
            <copyright>Copyright 2020, {$link}</copyright>
        </item>";
    }

    // Хидер для правильного отображения xml
    header('Content-Type: application/xml; charset=utf-8');

    return $content
        . '</channel>
           </rss>';
}