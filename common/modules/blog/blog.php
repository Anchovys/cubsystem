<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| blog.php [rev 1.0], Назначение: поддержка статей (блога)
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

class blog_module extends cs_module
{
    public $callback = ''; // file for callback
    private $_page = null;

    function __construct()
    {
        $this->name = "Blog";
        $this->description = "A simple blog realization.";
        $this->version = "1";
        $this->fullpath = CS__MODULESPATH . 'blog' . _DS;

        //require_once($this->fullpath . 'category.php');
        require_once($this->fullpath . 'page.php');

        $this->_page = new cs_page();
    }

    public function callback_load($data, $callback = false)
    {
        $callback = CS__TEMPLATE_VIEWS_DIR . $callback . '.php';
        if(!file_exists($f = $callback ? $callback : $this->callback))
            die("[blog] Can`t load template callback file : {$callback}");

        // return as buffer output
        return cs_return_output($f, $data);
    }

    public function get_pages_by($by, $data, $view_name = 'short-page_view')
    {
        $content = '';
        $pages = $this->_page->get_list_by($by, $data);

        if($pages['count'] > 0)
        {
            foreach($pages['result'] as $page_data)
            {
                $content .= $this->callback_load(['page' => new cs_page($page_data)], $view_name);
            }
            return $content;
        }
        else return false;
    }

    public function page404($view_name = 'short-page_view')
    {
        $data = [
            'comments'      => 0,
            'author'        => "",
            'views'         => 0,
            'link'          => "",
            'title'         => "Страница не найдена!",
            'context'       => "404 Страница не найдена!"
        ];
        return $this->callback_load(['page' => new cs_page($data)], $view_name);
    }
}
?>