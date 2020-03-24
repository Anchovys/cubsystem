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
    public $template = null;
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

    public function get_pages_by($by, $data, $view_name = 'short-page_view', $set_meta = TRUE)
    {
        $content = '';
        $pages = $this->_page->get_list_by($by, $data);

        if($pages['count'] > 0)
        {
            foreach($pages['result'] as $page_data)
            {
                $page = new cs_page($page_data);
                $content .= $this->template->callback_load(['page' => $page], $view_name);
                $this->template->meta_data = $page->meta;
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
        return $this->template->callback_load(['page' => new cs_page($data)], $view_name);
    }
}
?>