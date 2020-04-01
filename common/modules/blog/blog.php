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
    function __construct()
    {
        global $CS;
        $this->name = "Blog";
        $this->description = "A simple blog realization.";
        $this->version = "1";
        $this->fullpath = CS__MODULESPATH . 'blog' . _DS;

        //require_once($this->fullpath . 'category.php');
        require_once($this->fullpath . 'objects' . _DS . 'page.php');

        // make hook into render
        if($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__pre-template_hook', 'view', $this);
    }

    public function view()
    {
        global $CS;
        $segments = cs_get_segments();

        switch(isset($segments[0]) ? $segments[0] : '')
        {
            case 'page': // first segment = page
                $page = $this->getPagesBy("link", $page_tag = $segments[1], 'full-page_view');
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                break;

            case 'tag': // first segment = tag
                $page = $this->getPagesBy("tag", $page_tag = $segments[1], 'short-page_view', FALSE);
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Tag: {$page_tag}",
                    'description' => "Here you can see all page with tag: {$page_tag}"
                ]);
                break;

            case '':
            case 'home': // first segment = home or empty
                $page = $this->getPagesBy(FALSE, FALSE, 'short-page_view', FALSE);
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Home Page",
                    'description' => "Welcome to our home page!"
                ]);
                break;
        }
    }

    public function getPagesBy($by, $data, $view_name = 'short-page_view', $set_meta = TRUE)
    {
        global $CS;
        $content = '';
        $pages = cs_page::getListBy($by, $data);

        if(isset($pages['count']) && $pages['count'] > 0)
        {
            foreach($pages['result'] as $page_data)
            {
                $page = new cs_page($page_data);
                $content .= $CS->template->callbackLoad(['page' => $page], $view_name);
                if($set_meta) $CS->template->setMeta($page->meta);
            }
            return $content;
        }
        else return false;
    }

    public function page404($view_name = '404-page_view')
    {
        global $CS;
        $data = [
            'title'         => "Страница не найдена!",
            'context'       => "404 Страница не найдена!"
        ];
        return $CS->template->callbackLoad(['page' => new cs_page($data)], $view_name);
    }
}
?>