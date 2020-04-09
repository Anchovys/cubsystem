<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| blog.php [rev 1.4], Назначение: поддержка статей (блога)
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
        $this->version = "4";
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

        if($segments[1] === 'page_edit')
        {
            $CS->template->callbackLoad('', 'blog/page_edit_view', 'body');
        }
        if($segments[1] === 'page_list')
        {
            $CS->template->callbackLoad('', 'blog/page_list_view', 'body');
        }
        else if($segments[1] === 'addcat')
        {
            $CS->template->callbackLoad('', 'blog/addcat_view', 'body');
        }
    }

    public function admin_ajax()
    {
        global $CS;
        $segments = cs_get_segment();

        if(!isset($segments[2]))
            return;

        if($segments[2] === 'page_edit')
        {
            if (!isset($_POST['page-id']) || !isset($_POST['title']) || !isset($_POST['tag']) ||
            !isset($_POST['content']) || !isset($_POST['link'])  || !isset($_POST['author-id']) )
            {
                return;
            }

            $data = [];

            // айди
            // только для редактирования
            $id = cs_filter($_POST['page-id'], 'base;int');
            $data['id'] = $id;

            // титул
            $title = cs_filter($_POST['title']);
            $title = $title ? $title : 'no-title';
            $data['title'] = $title;

            // контекст
            $content = cs_filter($_POST['content'], 'multi_spaces;trim');
            $content = $content ? $content : 'no-content';

            if (strpos($content, '[xcut]'))
            {
                $content = explode('[xcut]', $content);

                $data['short_text'] = $content[0];
                $data['full_text'] = $content[1];

                $data['cut_type'] = 1;

            } else if (strpos($content, '[cut]'))
            {
                $content = explode('[cut]', $content);

                $data['short_text'] = $content[0];

                $data['full_text'] = $data['short_text'];
                $data['full_text'] .= $content[1];

                $data['cut_type'] = 2;
            } else // без разбивки
            {
                $data['full_text'] = $data['short_text'] = $content;
                $data['cut_type'] = 0;
            }

            // автор
            $author_id = $_POST['author-id'];
            if($author_id) // указан
            {
                // применим указанного автора
                $author_id = cs_filter($author_id, 'base;int');
            }
            else // не указан
                {
                    $author = $CS->gc('auth_module', 'modules')->getLoggedUser();
                    $author_id = $author->id;
                }
            $data['author'] = $author_id;

            // ссылка
            $link = $_POST['link'];
            $link = $link ? $link : cs_filter($title, 'transliterate;');
            $link = cs_filter($link, 'to_lower;spaces;special_string');
            $data['link'] = $link;

            // тег
            $tag = cs_filter($_POST['tag']);
            $data['tag'] = $tag;

            //категория
            $cat = isset($_POST['category']) && is_array($_POST['category']) ? $_POST['category'] : [];
            $data['cat_ids'] = $cat;

            // мета данные

            // титул
            $meta_title = cs_filter($_POST['meta_title']);
            $meta_title = $meta_title ? $meta_title : '';
            $data['meta_title'] = $meta_title;

            // описание
            $meta_desc = cs_filter($_POST['meta_description']);
            $meta_desc = $meta_desc ? $meta_desc : '';
            $data['meta_desc'] = $meta_desc;

            // создадим экземпляр страницы
            $page = new cs_page($data);

            // id редактируемой записи ноль. значит добавление
            if($id == 0)
            {
                $obj = $page->insert();
            }
            else // для значения не ноль, редактируем запись
            {
                $obj = $page->update();
            }

            // вернулся ли $obj
            die($obj === NULL ? 'fail' : 'success');

        }
        else if($segments[2] === 'page_del')
        {
            if(!isset($_POST['page-id']))
            {
                return;
            }

            // айди
            $id = cs_filter($_POST['page-id'], 'base;int');
            if(!$id)
                die('fail');

            $obj = cs_page::getById($id);

            die($obj['count'] === 0 || $obj['result']->delete() === FALSE ? 'fail' : 'success');
        }
        else if($segments[2] === 'add_cat')
        {
            if(!isset($_POST['name']) || !isset($_POST['descr']))
            {
                return;
            }

            $data = [];

            // название
            $name = cs_filter($_POST['name']);
            $name = $name ? $name : 'no-title';
            $data['name'] = $name;

            // ссылка
            $link = $_POST['link'];
            $link = $link ? $link : cs_filter($name, 'transliterate;');
            $link = cs_filter($link, 'to_lower;spaces;special_string');
            $data['link'] = $link;

            // описание
            $link = $_POST['link'];
            $link = $link ? $link : cs_filter($name, 'transliterate;');
            $link = cs_filter($link, 'to_lower;spaces;special_string');
            $data['link'] = $link;

            $data =
                [
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
                $page = $this->_displayPages(cs_page::getByLink($page_link = $segments[1]), 'blog/full-page_view');
                $CS->template->setBuffer('body', $page, FALSE);
                break;

            case 'tag': // first segment = tag
                $page = $this->_displayPages(cs_page::getListByTag($page_tag = $segments[1], $CS->template->getPagination(), ['cats', 'id', 'title', 'short_text', 'link', 'tag', 'author', 'views', 'comments'], TRUE), 'blog/short-page_view', FALSE);
                $CS->template->setBuffer('body', $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Tag: {$page_tag}",
                    'description' => "Here you can see all page with tag: {$page_tag}"
                ]);
                break;
            case 'category': // first segment = category

                $start = 1; // с какого индекса начинают идти категории (1)
                $end   = count($segments) - 1; // и заканчивают

                // если есть пагинация, то кончаться могут другим индексом
                // тогда конец категорий - это начало пагинаций
                if($pg = $CS->template->getPagination())
                {
                    // указано
                    if($start_id = $pg->getSegmentStartId())
                    {
                        // записываем
                        $end = $start_id;
                    }
                }

                // вырежем список категорий
                $cat_list = array_slice($segments, $start, $end);

                if(count($cat_list) === 0)
                    break;
                //if(count($cat_list) === 1)
                //{
                    $category = cs_cat::getByLink($cat_list[0], ['id']);
                    $page = cs_page::getByCategoryId($category->id, $CS->template->getPagination(), ['cats', 'id', 'title', 'short_text', 'link', 'tag', 'author', 'views', 'comments'], TRUE);
                //}
                //else
                //{
                //    $ids = [];
                //    foreach ($cat_list as $cat_link)
                //    {
                //        $category = cs_cat::getByLink($cat_link, ['id']);
                //        $ids[] = $category->id;
                //    }
                //    $page = cs_page::getByCategoryIds($ids);
                //}

                $page = $this->_displayPages($page, 'blog/short-page_view', FALSE);
                $CS->template->setBuffer('body', $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Cat: {$category->name}",
                    'description' => "Here you can see all page with cat: {$category->name}"
                ]);
                break;

            case '':
            case 'home': // first segment = home or empty
                $page = $this->_displayPages(cs_page::getListAll($CS->template->getPagination(), ['cats', 'id', 'title', 'short_text', 'link', 'tag', 'author', 'views', 'comments'], TRUE), 'blog/short-page_view', FALSE);
                $CS->template->setBuffer('body', $page, FALSE);
                $CS->template->setMeta([
                    'title' => "Home Page",
                    'description' => "Welcome to our home page!"
                ]);
                break;
        }
    }

    protected function _displayPages($pages, $view_name = 'blog/short-page_view', $set_meta = TRUE)
    {
        global $CS;
        $content = '';

        if(!isset($pages['count']) || $pages['count'] === 0)
        {
            return false;
        }
        elseif($pages['count'] > 1)
        {
            $total_pages = $pages['result'];

            foreach($total_pages as $page)
            {
                $content .= $CS->template->callbackLoad(['page' => $page], $view_name);
                if($set_meta) $CS->template->setMeta($page->meta);
            }

            if($p = $CS->template->getPagination())
                $CS->template->setBuffer('pagination', $p->getHtml());

        }
        elseif ($pages['count'] == 1)
        {
            $page = $pages['result'];

            if(is_array($page))
                $page = $page[array_key_first($page)];

            $content = $CS->template->callbackLoad(['page' => $page], $view_name);
            if($set_meta) $CS->template->setMeta($page->meta);
        }

        return $content;
    }
}
?>