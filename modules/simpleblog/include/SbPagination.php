<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class SbPagination
{
    private int $currentPage = 1;
    private int $limit       = 10;
    private int $total       = 0;
    private int $maxLinks    = 10;
    private int $amount      = 0;
    private string $index    = '?page=';
    private $indexSegId  = NULL;

    function __construct()
    {
        $this->currentPage = $this->curPage();
    }

    public function getOffset()
    {
        $totalPages = ceil($this->total / $this->limit);
        $page = max($this->currentPage, 1);
        $page = min($this->currentPage, $totalPages);
        $offset = ($page - 1) * $this->limit;
        if ($offset < 0) $offset = 0;
        return $offset;
    }

    public function setCurrentPage($page)
    {
        if($page < 1) return;
        $this->currentPage = $page;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($to)
    {
        $to = CsSecurity::filter($to, 'int');
        $this->limit = $to >= 1 ? $to : 1;
    }

    public function getSegmentStartId()
    {
        return empty($this->indexSegId) ? FALSE : $this->indexSegId;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal(int $to)
    {
        $to = CsSecurity::filter($to, 'int');
        $this->total = $to >= 1 ? $to : 1;
        $this->amount = $this->amount();
        return $this->total;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($to)
    {
        $this->index = $to;
        return $this->index;
    }

    public function setMaxLinks($to)
    {
        $to = CsSecurity::filter($to, 'int');
        $this->maxLinks = $to >= 1 ? $to : 1;
    }

    public function getHtml()
    {
        // найти количество ссылок
        $this->amount = $this->amount();

        // нечего выводить, уходим
        if($this->amount == 0 || $this->total < $this->limit)
            return "";

        // ссылки пока не созданы
        $links = NULL;

        // получим лимиты для цикла
        $limits = $this->limits();

        $html = "<ul class='pagination'>";

        // создаем ссылку для каждой страницы
        for ($page = $limits[0]; $page <= $limits[1]; $page++)
        {
            // ссылка - это текущая страница
            if ($page == $this->currentPage)
            {
                $links .= "<li class='page-item active'>";
                $links .= "<a href='#' class='page-link'>{$page}</a>";
                $links .= "</li>";
            } else $links .= $this->generateHtml($page);
        }

        // ссылки были созданы
        if (!is_null($links))
        {
            // создадим левую границу
            $links = $this->generateHtml(($this->currentPage > 1) ? 1 : 0, 'Первая') .
                $this->generateHtml(($this->currentPage > 1) ? ($this->currentPage - 1) : 0, 'Назад') . $links;

            $links .= $this->generateHtml( ($this->currentPage < $this->amount) ? ($this->currentPage + 1) : 0, 'Вперед');
            $links .= $this->generateHtml(($this->currentPage < $this->amount) ? $this->amount : 0, 'Последняя');

        }

        $html .= "{$links}</ul>";

        # Возвращаем html
        return $html;
    }

    /**
     * Создает ссылку на конкретную страницу
     * @param $page
     * @param bool $text
     * @return string
     */
    private function generateHtml(int $page, $text = FALSE)
    {
        // текст не задан.
        // используем номер страницы
        if (!$text) $text = $page;

        // получаем полный url, но уберем из url ссылку на номер страницу
        //$url = preg_replace("~".$this->index."/[0-9]+~", '', CsUrl::fullUrl());

        $url = CsUrl::baseUrl() . 'home';

        // если нулевой индекс не найден, то тогда нужно
        // добавить на его место /home/
        // так, чтобы было mysite/home ...
        //if(!CsUrl::segment(0)) $url .= 'home';

        // дописать в конец слеш
        $url .= substr($url, -1) == '/' ? '' : '/';

        // формируем ссылку на страницу
        if($page !== 1)
            $url .= "{$this->index}{$page}";

        // выводим html
        if($page > 0)
        {
            $html =  "<li class='page-item'>";
            $html .= "<a href='{$url}' class='page-link'>{$text}</a>";
            $html .= "</li>";
        } else
        {
            $html =  "<li class='page-item disabled'>";
            $html .= "<a href='#' class='page-link'>{$text}</a>";
            $html .= "</li>";
        }

        return $html;
    }

    /**
     * Левые, правые границы для вывода ссылок пагинации
     * @return array
     */
    private function limits()
    {
        $left = $this->currentPage - round($this->maxLinks / 2);
        $start = $left > 0 ? $left : 1;

        if ($start + $this->maxLinks > $this->amount)
        {
            $end = $this->amount;
            $start = $this->amount - $this->maxLinks > 0 ? $this->amount - $this->maxLinks : 1;
        }
        else $end = $start > 1 ? $start + $this->maxLinks : $this->maxLinks;

        return array($start, $end);
    }

    private function amount()
    {
        return ceil($this->total / $this->limit);
    }

    /**
     * Функция определяющяя текущий номер страницы пагинации
     * @return int
     */
    private function curPage()
    {
        // получаем массив сегментов
        $segments = CsUrl::segment();
        // по дефолту страница первая
        $currentPage = 1;
        // ищем есть ли в массиве сегментов элемент 'next' (определяемый в index)
        if ($key = array_search($this->index, $segments))
        {
            // значит следующий идущий будет id страницы
            if (isset($segments[$key + 1]))
            {
                $key = CsSecurity::filter($segments[$key + 1], 'int');
                if(!$key) $key = 1;
            }

            // сохраним с какого сегмента идет пагинация
            $this->indexSegId = $key;

            // id страницы больше чем ноль
            if ($key > 0)
                $currentPage = $key;
        }

        return (int)$currentPage;
    }
}