<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| page.php, Назначение: класс страницы - основная единица блога
| -------------------------------------------------------------------------
| В этом файле описан класс страницы, который используется для вывода записи,
| А также, содержатся функции для работы с базой данных
| *** Для работы класса необходим хелпер mysqli_db ***
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class cs_page
{
    public $id          = NULL;
    public $title       = NULL;
    public $tag         = NULL;
    public $cat         = NULL;
    public $cat_ids     = NULL;
    public $context     = NULL;
    public $comments    = NULL;
    public $author      = NULL;
    public $author_id   = NULL;
    public $views       = NULL;
    public $link        = NULL;
    public $meta        = [];

    function __construct($data = [], $needle = FALSE)
    {
        if(is_array($data))
        {
            // all basic data
            if (!$needle || in_array('id', $needle))
            if (isset($data['id']))         $this->id       = (int)$data['id'];
            if (!$needle || in_array('title', $needle))
            if (isset($data['title']))      $this->title    = (string)$data['title'];
            if (!$needle || in_array('tag', $needle))
            if (isset($data['tag']))        $this->tag      = (string)$data['tag'];
            if (!$needle || in_array('comments', $needle))
            if (isset($data['comments']))   $this->comments = (int)$data['comments'];
            if (!$needle || in_array('views', $needle))
            if (isset($data['views']))      $this->views    = (int)$data['views'];
            if (!$needle || in_array('link', $needle))
            if (isset($data['link']))       $this->link     = (string)$data['link'];
            if (!$needle || in_array('context', $needle))
            if (isset($data['context']))    $this->context  = (string)$data['context'];
            if (!$needle || in_array('author', $needle))
            if (isset($data['author'])) {
                $this->author_id   = intval($data['author']);
                $this->author      = cs_user::getById($this->author_id);
            }
            if (!$needle || in_array('cat', $needle))
            if (isset($data['cat'])) {
                $ids = explode(',', $data['cat']);
                $category_data = cs_cat::getByIds($ids);
                if($category_data !== NULL && $category_data && $category_data['count'] !== 0)
                {
                    $this->cat_ids = $ids;
                    $this->cat = $category_data['result'];
                }
            }
            if (!$needle || in_array('cat_ids', $needle))
            {
                $ids = explode(',', $data['cat']);
                $this->cat_ids = $ids;
            }

            // meta data
            if (!$needle || in_array('meta', $needle))
            {
                if (isset($data['title']))      $this->meta['title']   = (string)$data['title'];
                if (isset($data['context']))    $this->meta['context'] = (string)$data['title'];
            }
        }
    }

    public static function getById($id = FALSE)
    {
        $id = cs_filter($id, 'int');
        if(!$id) return NULL;
        return self::getBy($id, 'id');
    }

    public static function getByLink($link = FALSE)
    {
        $link = cs_filter($link, 'base');
        if(!$link || !is_string($link)) return NULL;
        return self::getBy($link, 'link');
    }

    private static function getBy($sel = FALSE, $by = 'id')
    {
        global $CS;

        if(!$by || !$sel) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where($by, $sel);

        if(!$data = $db->getOne('pages'))
            return NULL;

        return
            [
                'count'   => 1,
                'result' =>  new cs_page($data)
            ];


    }

    public static function getByIds($ids = [], $pagination = FALSE, $needle = FALSE)
    {
        global $CS;

        if(!$ids || !is_array($ids)) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where('id', $ids);

        if($pagination === FALSE)
        {
            if(!$objects = $db->get('pages'))
                return NULL;
        } else
        {
            $db->pageLimit = $pagination->getLimit();
            $objects = $db->arraybuilder()->paginate("pages", $pagination->getCurrentPage());

            $pagination->setTotal($db->totalPages);

            if(!$objects)
                return NULL;
        }

        $result = [];
        foreach ($objects as $item)
            $result[] = new cs_page($item, $needle);

        return
            [
                'count'   => count($result),
                'result' =>  $result
            ];
    }

    public static function getByCategoryId($id, $pagination = FALSE)
    {
        $non = [ 'count'  =>  0, 'result' =>  [] ];

        $totally = [];
        $pages = self::getListBy(FALSE, FALSE, FALSE, ['id', 'cat_ids']);

        if($pages['count'] === 0)
            return $non;

        $pages = $pages['result'];

        foreach ($pages as $page)
        {
            if(!is_array($page->cat_ids))
                continue;

            if(in_array($id, $page->cat_ids))
                $totally[] = $page->id;
        }

        $totally = cs_page::getByIds($totally, $pagination);

        return $totally;
    }

    /*
    public static function getByCategoryId1($id, $pagination = FALSE)
    {
        global $CS;;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $id = cs_filter($id, 'int');
        if(!$id) return NULL;

        $db->where('cat_id', $id);

        if(!$matches = $db->get('cat_pages'))
            return FALSE;

        $page_ids = [];
        foreach ($matches as $math)
            $page_ids [] = $math['page_id'];


        return self::getByIds($page_ids, $pagination);
    }*/

    public static function getListByTag($tag = FALSE, $pagination = FALSE, $needle = FALSE)
    {
        $tag = cs_filter($tag, 'base');
        if(!$tag || !is_string($tag)) return NULL;
        return self::getListBy($tag, 'tag', $pagination, $needle);
    }

    public static function getListBy($sel = FALSE, $by = 'id', $pagination = FALSE, $needle = FALSE)
    {
        global $CS;

        if(!$db = $CS->gc('mysqli_db_helper', 'helpers')->getInstance())
            die('[blog] Can`t connect to database');

        if($by && is_string($by) && $sel)
            $db->where($by, $sel);
        
        if($pagination === FALSE)
        {
            if(!$objects = $db->get('pages'))
                return NULL;
        } else
        {
            $db->pageLimit = $pagination->getLimit();
            $objects = $db->arraybuilder()->paginate("pages", $pagination->getCurrentPage());

            $pagination->setTotal($db->totalPages);

            if(!$objects)
                return NULL;
        }

        $result = [];
        foreach ($objects as $item)
            $result[] = new cs_page($item, $needle);

        return
        [
            'count'      =>  count($result),
            'result'     =>  $result
        ];
    }

    public function insert()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $pages = self::getListBy( $this->link, 'link');
        if($pages && $pages['count'] !== 0)
           return NULL;

        $data = [
            'title'     => $this->title,
            'tag'       => $this->tag,
            'cat'       => is_array($this->cat_ids) ? implode(',', $this->cat_ids) : '',
            'comments'  => 0,
            'views'     => 0,
            'author'    => $this->author_id,
            'link'      => $this->link,
            'context'   => $this->context
        ];

        // function returned current page id
        $page_id = $db->insert('pages', $data);

        if(!is_int($page_id))
            return NULL;

        /*
        if(is_array($this->cat_ids))
        {
            foreach ($this->cat_ids as $cat_id)
            {
                $data = [
                    'page_id' => intval($page_id),
                    'cat_id'  => intval($cat_id)
                ];

                $db->insert('cat_pages', $data);
            }
        }*/

        // get user from database
        return self::getById($page_id);
    }
}
?>