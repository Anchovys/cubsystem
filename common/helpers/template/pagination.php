<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| pagination.php, Назначение: постраничная навигация
| Файл подключается в template.php, в зависимости от настроек шаблона
| -------------------------------------------------------------------------
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class cs_pagination
{
    private $currentPage = 1;
    private $limit       = 10;
    private $total       = 0;
    private $maxLinks    = 10;
    private $amount      = 0;
    private $index       = 'next';
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
        $to = cs_filter($to, 'int');
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

    public function setTotal($to)
    {
        $to = cs_filter($to, 'int');
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
        $to = cs_filter($to, 'int');
        $this->maxLinks = $to >= 1 ? $to : 1;
    }

    public function getHtml()
    {
        // найти количество ссылок
        $this->amount = $this->amount();

        // нечего выводить, уходим
        if($this->amount == 0 || $this->total < $this->limit)
            return;

        // ссылки пока не созданы
        $links = NULL;

        // получим лимиты для цикла
        $limits = $this->limits();

        $html = "<ul class='cs_pagination'>";

        // создаем ссылку для каждой страницы
        for ($page = $limits[0]; $page <= $limits[1]; $page++)
        {
            // ссылка - это текущая страница
            if ($page == $this->currentPage)
            {
                $links .= "<li class='cs_pagin_item_active'>";
                $links .= "<a href='#' class='cs_pagin_link_active'>{$page}</a>";
                $links .= "</li>";
            } else $links .= $this->generateHtml($page);
        }

        // ссылки были созданы
        if (!is_null($links))
        {
            // создадим левую границу
            if ($this->currentPage > 1)
                $links = $this->generateHtml(1, '&lt;') . $links;

            // создадим правую границу
            if ($this->currentPage < $this->amount)
                $links .= $this->generateHtml($this->amount, '&gt;');
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
    private function generateHtml($page, $text = FALSE)
    {
        // текст не задан.
        // используем номер страницы
        if (!$text) $text = $page;

        // получаем полный url, но уберем из url ссылку на номер страницу
        $url = preg_replace("~".$this->index."/[0-9]+~", '', cs_full_url());

        // если нулевой индекс не найден, то тогда нужно
        // добавить на его место /home/
        // так, чтобы было mysite/home ...
        if(!cs_get_segment(0)) $url .= 'home';

        // дописать в конец слеш
        $url .= substr($url, -1) == '/' ? '' : '/';

        // формируем ссылку на страницу
        $url .= "{$this->index}/{$page}";

        // выводим html
        $html =  "<li class='cs_pagin_item'>";
        $html .= "<a href='{$url}' class='cs_pagin_link'>{$text}</a>";
        $html .= "</li>";

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
        return round($this->total / $this->limit);
    }

    /**
     * Функция определяющяя текущий номер страницы пагинации
     * @return int
     */
    private function curPage()
    {
        // получаем массив сегментов
        $segments = cs_get_segment();
        // по дефолту страница первая
        $currentPage = 1;
        // ищем есть ли в массиве сегментов элемент 'next' (определяемый в index)
        if ($key = array_search($this->index, $segments))
        {
            // значит следующий идущий будет id страницы
            if (isset($segments[$key + 1]))
            {
                $key = cs_filter($segments[$key + 1], 'int');
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
?>