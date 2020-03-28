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
    function __construct()
    {
        global $CS;
        $this->name = "Blog";
        $this->description = "A simple blog realization.";
        $this->version = "1";
        $this->fullpath = CS__MODULESPATH . 'blog' . _DS;

        //require_once($this->fullpath . 'category.php');
        require_once($this->fullpath . 'objects' . _DS . 'page.php');

        // get template
        if(!$this->template = $CS->gc('template_helper', 'helpers'))
            die('Can`t load template helper');

        // make hook into render
        if($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__pre-template_hook', 'view', $this);
    }

    public function view()
    {
        global $CS;
        $segments = cs_get_segments();

        switch($segments[0])
        {
            case 'login':
                $this->template->body_buffer .= $this->template->callbackLoad([], 'loginform_view');
                $this->template->meta_data = [
                    'title' => "Authorization",
                    'description' => "You can auth with you login/password"
                ];
                break;
            case 'register':
                $this->template->body_buffer .= $this->template->callbackLoad([], 'registerform_view');
                $this->template->meta_data = [
                    'title' => "Registration",
                    'description' => "You can register on website"
                ];
                break;

            case 'page': // first segment = page
                $page = $this->getPagesBy("link", $page_tag = $segments[1], 'full-page_view');
                $this->template->body_buffer .= (!$page) ? $this->page404('short-page_view') : $page;
                break;

            case 'tag': // first segment = tag
                $page = $this->getPagesBy("tag", $page_tag = $segments[1], 'short-page_view', FALSE);
                $this->template->body_buffer .= (!$page) ? $this->page404('short-page_view') : $page;
                $this->template->meta_data = [
                    'title' => "Tag: {$page_tag}",
                    'description' => "Here you can see all page with tag: {$page_tag}"
                ];
                break;

            case '':
            case 'home': // first segment = home or empty
                $page = $this->getPagesBy(false, false, 'short-page_view', FALSE);
                $this->template->body_buffer .= (!$page) ? $this->page404('short-page_view') : $page;
                $this->template->meta_data = [
                    'title' => "Home Page",
                    'description' => "Welcome to our home page!"
                ];
                break;
        }
    }

    public function getPagesBy($by, $data, $view_name = 'short-page_view', $set_meta = TRUE)
    {
        $content = '';
        $pages = cs_page::getListBy($by, $data);

        if($pages['count'] > 0)
        {
            foreach($pages['result'] as $page_data)
            {
                $page = new cs_page($page_data);
                $content .= $this->template->callbackLoad(['page' => $page], $view_name);
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
        return $this->template->callbackLoad(['page' => new cs_page($data)], $view_name);
    }
}
?>