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
    public $cats        = NULL;
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
            if (isset($data['id']) && (!$needle || in_array('id', $needle)))
                $this->id = (int)$data['id'];
            if (isset($data['title']) && (!$needle || in_array('title', $needle)))
                $this->title = (string)$data['title'];
            if (isset($data['tag']) && (!$needle || in_array('tag', $needle)))
                $this->tag = (string)$data['tag'];
            if (isset($data['comments']) && (!$needle || in_array('comments', $needle)))
                $this->comments = (int)$data['comments'];
            if (isset($data['views']) && (!$needle || in_array('views', $needle)))
                $this->views = (int)$data['views'];
            if (isset($data['link']) && (!$needle || in_array('link', $needle)))
                $this->link = (string)$data['link'];
            if (isset($data['context']) && (!$needle || in_array('context', $needle)))
                $this->context = (string)$data['context'];
            if (isset($data['author']) && (!$needle || in_array('author', $needle)))
            {
                $this->author_id = intval($data['author']);
                $this->author = cs_user::getById($this->author_id);
            }

            /*
            if (!$needle || in_array('cat_ids', $needle))
            {
                $ids = explode(',', $data['cats']);
                $this->cat_ids = $ids;
            }
            if (isset($data['cats']) && (!$needle || in_array('cats', $needle)))
            {
                $ids = explode(',', $data['cats']);
                $category_data = cs_cat::getByIds($ids);
                if($category_data !== NULL && $category_data && $category_data['count'] !== 0)
                {
                    $this->cat_ids = $ids;
                    $this->cats = $category_data['result'];
                }
            }*/

            if (!$needle || in_array('cat_ids', $needle))
            {
                if($this->id !== NULL)
                {
                    $ids = [];
                    $cats = cs_cat::getByPageId($this->id, ['id']);
                    if($cats['count'] !== 0)
                    {
                        foreach ($cats['result'] as $cat)
                        {
                            $ids[] = $cat->id;
                        }
                    }

                    $this->cat_ids = $ids;
                }
            }

            if ((!$needle || in_array('cats', $needle)))
            {
                if($this->id !== NULL) {
                    $this->cats = cs_cat::getByPageId($this->id)['result'];
                }
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

        $db->where('id', $ids, 'IN');

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

    public static function getByCategoryId($id, $pagination = FALSE)
    {
        global $CS;

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
    }

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
        //  'cat'       => is_array($this->cat_ids) ? implode(',', $this->cat_ids) : '',
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
        }

        // get user from database
        return self::getById($page_id);
    }
}
?>