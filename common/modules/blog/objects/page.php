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
    public $views       = NULL;
    public $link        = NULL;
    public $meta        = [];

    function __construct($data = [])
    {
        if(is_array($data))
        {
            // all basic data
            if (isset($data['id']))         $this->id       = (int)$data['id'];
            if (isset($data['title']))      $this->title    = (string)$data['title'];
            if (isset($data['tag']))        $this->tag      = (string)$data['tag'];
            if (isset($data['comments']))   $this->comments = (int)$data['comments'];
            if (isset($data['author']))     $this->author   = (string)$data['author'];
            if (isset($data['views']))      $this->views    = (int)$data['views'];
            if (isset($data['link']))       $this->link     = (string)$data['link'];
            if (isset($data['context']))    $this->context  = (string)$data['context'];
            if (isset($data['cat'])) {
                $ids = explode(',', $data['cat']);
                $category_data = cs_cat::getByIds($ids);
                if($category_data !== NULL && $category_data && $category_data['count'] !== 0)
                {
                    $this->cat_ids = $ids;
                    $this->cat = $category_data['result'];
                }
            }


            // meta data
            if (isset($data['title']))      $this->meta['title']   = (string)$data['title'];
            if (isset($data['context']))    $this->meta['context'] = (string)$data['title'];
        }
    }

    public static function getById($id = FALSE)
    {
        global $CS;

        if(!$id) return NULL;

        $id = intval($id);

        if(!$db = $CS->database->getInstance())
                die('[blog] Can`t connect to database');

        $db->where('id', $id);
        
        if($data = $db->getOne('pages'))
            return new cs_page($data);

        return NULL;
    }

    public static function getByIds($ids = [])
    {
        global $CS;

        if(!$ids || !is_array($ids)) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where('id', $ids);

        if(!$objects = $db->get('pages'))
            return FALSE;

        $result = [];
        foreach ($objects as $item)
            $result[] = new cs_page($item);

        return
            [
                'count'   => count($result),
                'result' =>  $result
            ];
    }

    public static function getByCategory($id)
    {
        $totally = [];
        $pages = self::getListBy(FALSE, FALSE);

        if($pages['count'] === 0)
            return [];

        $pages = $pages['result'];

        foreach ($pages as $page)
        {
            if(!is_array($page->cat_ids))
                continue;

            if(in_array($id, $page->cat_ids))
                $totally[] = $page;
        }

        return
            [
                'count'   => count($totally),
                'result' =>  $totally
            ];
    }

    public static function getListBy($by = FALSE, $value = FALSE)
    {
        global $CS;

        if(!$db = $CS->gc('mysqli_db_helper', 'helpers')->getInstance())
            die('[blog] Can`t connect to database');

        if($by && is_string($by) && $value)
            $db->where($by, $value);
        
        if(!$objects = $db->get('pages'))
            return FALSE;

        $result = [];
        foreach ($objects as $item)
            $result[] = new cs_page($item);

        return
        [
            'count'   => count($result),
            'result' =>  $result            
        ];
    }

    public function insert()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $pages = self::getListBy('link', $this->link);
        if($pages !== FALSE && $pages['count'] !== 0)
           return NULL;

        $data = [
            'title'     => $this->title,
            'tag'       => $this->tag,
            'cat'       => is_array($this->cat_ids) ? implode(',', $this->cat_ids) : '',
            'comments'  => 0,
            'views'     => 0,
            'author'    => $this->author,
            'link'      => $this->link,
            'context'   => $this->context
        ];

        // function returned current page id
        $id = $db->insert('pages', $data);

        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }
}
?>