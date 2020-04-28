<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| rss_feed.php, Назначение: вывод xss ленты
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
function rss_feed_check()
{
    $segments = cs_get_segment();
    $last_segment = $segments[count($segments)-1];

    return $last_segment === 'feed';
}

function rss_mk_item(?array $data)
{
    $content = "<title>{$data['title']}</title>
                <link>{$data['link']}</link>
                <description>{$data['description']}</description>
                <pubDate>{$data['pubDate']}</pubDate>
                <language>en-ru</language>
                <generator>Cubsystem cms</generator>
                <copyright>Copyright 2020, {cs_abs_url()}</copyright>";
    return $content;
}

// вывести строку ленты по данным
function rss_feed_display($data_pages)
{
    if(!isset($data_pages['count']) || $data_pages['count'] === 0)
    {
        return false;
    }

    $total_pages = $data_pages['result'];

    $content = '<?xml version="1.0" encoding="utf-8"?>
                <rss version="2.0">
                <channel>';

    if($data_pages['count'] === 1)
    {
        $page = $total_pages;

        $data = [];
        $data['title'] = $page->meta['title'];
        $data['description'] = $page->meta['description'];
        $data['pubDate'] = $page->date;
        $data['link'] = cs_abs_url('page/' . $page->link);

        $content .= rss_mk_item($data);
    } else
    {

        $data = [];
        $data['title'] = 'Sample blog';
        $data['description'] = 'Sample blog description';
        $data['pubDate'] = '';
        $data['link'] = cs_abs_url();

        $content .= rss_mk_item($data);

        foreach($total_pages as $page)
        {
            $data = [];
            $data['title'] = $page->meta['title'];
            $data['description'] = $page->meta['description'];
            $data['pubDate'] = $page->date;
            $data['link'] = cs_abs_url('page/' . $page->link);

            $content .= "<item>";
            $content .= rss_mk_item($data);
            $content .= "</item>";
        }
    }

    // Хидер для правильного отображения xml
    header('Content-Type: application/xml; charset=utf-8');

    return $content
        . '</channel>
           </rss>';
}