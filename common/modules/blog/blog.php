<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| blog.php [rev 1.3], Назначение: поддержка статей (блога)
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
        $this->version = "3";
        $this->fullpath = CS_MODULESCPATH . 'blog' . _DS;

        require_once($this->fullpath . 'objects' . _DS . 'category.php');
        require_once($this->fullpath . 'objects' . _DS . 'page.php');

        if($h = $CS->gc('hooks_helper', 'helpers'))
        {
            // make hook into render
            $h->register('cs__pre-template_hook', 'view', $this);

            // admin hooks
            $h->register('cs__admin-view', 'admin_view',  $this);
            $h->register('cs__admin-ajax', 'admin_ajax',  $this);
        }
    }

    public function admin_view()
    {
        global $CS;
        $segments = cs_get_segment();

        if(!isset($segments[1]))
            return;

        if($segments[1] === 'addpage')
            $CS->template->callbackLoad('', 'blog/addpage_view', 'body');
        else if($segments[1] === 'addcat')
            $CS->template->callbackLoad('', 'blog/addcat_view', 'body');
    }

    public function admin_ajax()
    {
        global $CS;
        $segments = cs_get_segment();

        if(!isset($segments[2]))
            return;

        if($segments[2] === 'add_page')
        {
            if(!isset($_POST['title'])    || !isset($_POST['tag']) || !isset($_POST['cat']) ||
               !isset($_POST['content'])  || !isset($_POST['author']))
            {
                return;
            }

            $data = [
                'title'     => cs_filter($_POST['title']),
                'context'   => cs_filter($_POST['content'], 'multi_spaces;trim'),
                'author'    => cs_filter($_POST['author']),
                'link'      => cs_rnd_str(3),
                'tag'       => cs_filter($_POST['tag']),
                'cat'       => cs_filter($_POST['cat'])
            ];

            $page = new cs_page($data);
            $obj = $page->insert();
            die($obj === NULL ? 'fail' : 'success');
        } else if($segments[2] === 'add_cat')
        {
            if(!isset($_POST['name']) || !isset($_POST['link']) || !isset($_POST['descr']))
            {
                return;
            }

            $data = [
                'name'           => cs_filter($_POST['name']),
                'link'           => cs_filter($_POST['link']),
                'description'    => cs_filter($_POST['descr'])
            ];

            $cat = new cs_cat($data);
            $obj = $cat->insert();
            die($obj === NULL ? 'fail' : 'success');
        }
    }

    public function view()
    {
        global $CS;
        $segments = cs_get_segment();

        switch(isset($segments[0]) ? $segments[0] : '')
        {
            case 'page': // first segment = page
                $page = $this->getPagesBy("link", $page_tag = $segments[1], 'blog/full-page_view');
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                break;

            case 'tag': // first segment = tag
                $page = $this->getPagesBy("tag", $page_tag = $segments[1], 'blog/short-page_view', FALSE);
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Tag: {$page_tag}",
                    'description' => "Here you can see all page with tag: {$page_tag}"
                ]);
                break;
            case 'cat': // first segment = cat
                $page = $this->displayPages(cs_page::getByCategory($page_cat = $segments[1]), 'blog/short-page_view', FALSE);
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Tag: {$page_cat}",
                    'description' => "Here you can see all page with tag: {$page_cat}"
                ]);
                break;

            case '':
            case 'home': // first segment = home or empty
                $page = $this->displayPages(cs_page::getListBy(FALSE,FALSE), 'blog/short-page_view', FALSE);
                $CS->template->setBuffer('body', (!$page) ? $this->page404() : $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Home Page",
                    'description' => "Welcome to our home page!"
                ]);
                break;
        }
    }

    public function displayPages($pages, $view_name = 'blog/short-page_view', $set_meta = TRUE)
    {
        global $CS;
        $content = '';
        if(isset($pages['count']) && $pages['count'] > 0)
        {
            $pages['result'] = array_reverse($pages['result']);
            foreach($pages['result'] as $page)
            {
                $content .= $CS->template->callbackLoad(['page' => $page], $view_name);
                if($set_meta) $CS->template->setMeta($page->meta);
            }
            return $content;
        }
        else return false;
    }

    public function page404($view_name = 'blog/404-page_view')
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