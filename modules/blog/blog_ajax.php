<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

//////////
/// Выполнение ajax
/// Из файла /blog/blog.php
//////////

function __ajax()
{
    global $CS;
    $segments = cs_get_segment();

    if(!isset($segments[2]))
        return;

    // кто
    $user = $CS->gc('auth_module', 'modules')->getLoggedUser();
    if(!$user->isAdmin())
        die('Wrong.');

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
            $data['full_text']  = $content[1];

            $data['cut_type'] = 1;

        } else if (strpos($content, '[cut]'))
        {
            $content = explode('[cut]', $content);

            $data['short_text'] = $content[0];

            $data['full_text']  = $data['short_text'];
            $data['full_text'] .= $content[1];

            $data['cut_type'] = 2;
        } else // без разбивки
        {
            $data['full_text'] = $data['short_text'] = $content;
            $data['cut_type'] = 0;
        }

        // автор
        $author_id = $_POST['author-id'];
        $author_id = $author_id ? cs_filter($author_id, 'base;int') : $user->id;
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