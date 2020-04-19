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
    public $short_text  = NULL;
    public $full_text   = NULL;
    public $cut_type    = NULL;
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
            if (isset($data['short_text']) && (!$needle || in_array('short_text', $needle)))
                $this->short_text = (string)$data['short_text'];
            if (isset($data['full_text']) && (!$needle || in_array('full_text', $needle)))
                $this->full_text = (string)$data['full_text'];
            if (isset($data['cut_type']) && (!$needle || in_array('cut_type', $needle)))
                $this->cut_type = (int)$data['cut_type'];
            if (isset($data['author']) && (!$needle || in_array('author', $needle)))
            {
                $this->author_id = intval($data['author']);
                $this->author = cs_user::getById($this->author_id, ['name', 'id']);
            }

            if(isset($data['cat_ids']) && is_array($data['cat_ids']))
            {
                $this->cat_ids = $data['cat_ids'];
            }
            else if (!$needle || in_array('cat_ids', $needle))
            {
                if($this->id !== NULL)
                {
                    $ids = [];
                    $cats = cs_cat::getByPageId($this->id, ['id']);
                    if($cats && $cats['count'] !== 0)
                    {
                        foreach ($cats['result'] as $cat)
                        {
                            $ids[] = $cat->id;
                        }
                    }
                    $this->cat_ids = $ids;
                }
                else
                    $this->cat_ids = isset($data['cat']) ? explode(',', $data['cat']) : '';
            }

            if ((!$needle || in_array('cats', $needle)))
            {
                if($this->id !== NULL && $cats = cs_cat::getByPageId($this->id, ['name', 'link'])) {
                    $this->cats = $cats['result'];
                }
            }

            // meta data
            if (!$needle || in_array('meta', $needle))
            {
                $this->meta['title'] = isset($data['meta_title']) && $data['meta_title'] ? (string)$data['meta_title'] :
                    isset($data['title']) && $data['title'] ? (string)$data['title'] : '';

                $this->meta['description'] = isset($data['meta_desc']) ? (string)$data['meta_desc'] : '';
            }
        }
    }

    public static function getById($id = FALSE)
    {
        $id = cs_filter($id, 'int');
        if(!$id) return NULL;
        return self::_getBy($id, 'id');
    }

    public static function getByLink($link = FALSE)
    {
        $link = cs_filter($link, 'base');
        if(!$link || !is_string($link)) return NULL;
        return self::_getBy($link, 'link');
    }

    protected static function _getBy($sel = FALSE, $by = 'id')
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

    public static function getByIds($ids = [], $pagination = FALSE, $needle = FALSE, $reverse = FALSE)
    {
        global $CS;

        if(!$ids || !is_array($ids)) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        if($reverse)
            $db->orderBy('id', 'desc');
        $db->where('id', $ids, 'IN');

        if($pagination === FALSE)
        {
            if(!$objects = $db->get('pages'))
                return NULL;
        } else
        {
            $db->pageLimit = $pagination->getLimit();
            $objects = $db->arraybuilder()->paginate("pages", $pagination->getCurrentPage());
            $pagination->setTotal($db->totalCount);

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

    public static function getByCategoryId($id, $pagination = FALSE, $needle = FALSE, $reverse = TRUE)
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        // фильтруем id
        if(!$id = cs_filter($id, 'int'))
        {
            return NULL;
        }

        // обратная сортировка (если требуется)
        if($reverse)
        {
            $db->orderBy('id', 'desc');
        }

        $db->where('cat_id', $id);

        // пагинация отключена
        if($pagination === FALSE)
        {
            // просто выдергиваем все данные
            if (!$matches = $db->get('cat_pages'))
                return FALSE;
        }
        else
        {
            // количество элементов на страницу
            $db->pageLimit = $pagination->getLimit();

            // по пагинации
            if(!$matches = $db->arraybuilder()->paginate("cat_pages", $pagination->getCurrentPage(), ['page_id']))
                return FALSE;

            // для пагинации ставим общее кол-во элементов
            $pagination->setTotal($db->totalCount);
        }

        // массив с id страниц под вывод
        $page_ids = [];

        // вытаскиваем id страниц
        foreach ($matches as $math)
            $page_ids [] = $math['page_id'];

        return self::getByIds($page_ids, FALSE, $needle, FALSE);
    }

    /*
    public static function getByCategoryIds($ids = [], $pagination = FALSE, $needle = FALSE, $reverse = TRUE)
    {
        global $CS;

        // лимит до 3х вложений
        $ids = array_slice($ids, 0 , 3);

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where('cat_id', $ids, 'IN');

        // обратная сортировка (если требуется)
        //if($reverse)
        //{
        //    $db->orderBy('page_id', 'desc');
        //}

        if (!$matches = $db->get('cat_pages'))
            return FALSE;

        // наполняем массив типа [page_id] => cats[]
        $matches_n = [];
        foreach ($matches as $match)
            $matches_n[$match['page_id']] [] = $match['cat_id'];

        // массив с id страниц под вывод
        $page_ids = [];
        foreach ($matches_n as $key=>$item) {
            $checks = true;
            foreach ($ids as $id)
                if (!in_array($id, $item)) {
                    $checks = false;
                    return;
                }
            if($checks) {
                $page_ids[] = $key;
            }
        }

        return self::getByIds($page_ids, $pagination, $needle, FALSE);
    }*/


    public static function getListByTag($tag = FALSE, $pagination = FALSE, $needle = FALSE, $reverse = FALSE)
    {
        $tag = cs_filter($tag, 'base');
        if(!$tag || !is_string($tag)) return NULL;
        return self::_getListBy($tag, 'tag', $pagination, $needle, $reverse);
    }

    public static function getListAll($pagination = FALSE, $needle = FALSE, $reverse = FALSE)
    {
        return self::_getListBy(FALSE, FALSE, $pagination, $needle, $reverse);
    }

    protected static function _getListBy($sel = FALSE, $by = 'id', $pagination = FALSE, $needle = FALSE, $reverse = FALSE)
    {
        global $CS;

        if(!$db = $CS->gc('mysqli_db_helper', 'helpers')->getInstance())
            die('[blog] Can`t connect to database');

        if($reverse)
            $db->orderBy('id', 'desc');
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
            $pagination->setTotal($db->totalCount);
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

    public function delete()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        // если id не указан
        if(!$this->id)
            return FALSE;

        // вычищаем категории
        $db->where('page_id', $this->id);
        $db->delete('cat_pages');

        // вычищаем данные страницы
        $db->where('id', $this->id);
        return $db->delete('pages');
    }

    public function update()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $data =
            [
                'id'            => $db->escape($this->id),
                'title'         => $db->escape($this->title),
                'tag'           => $db->escape($this->tag),
                'author'        => $db->escape($this->author_id),
                'link'          => $db->escape($this->link),
                'short_text'    => $db->escape($this->short_text),
                'full_text'     => $db->escape($this->full_text),
                'cut_type'      => $db->escape($this->cut_type),
                'meta_title'    => $db->escape($this->meta['title']),
                'meta_desc'     => $db->escape($this->meta['description'])
            ];

        // обновляем данные о странице
        $db->update('pages', $data);

        $for_remove = [];
        $for_add    = $this->cat_ids;

        $cats = cs_cat::getByPageId($this->id, ['id']);

        foreach ($cats['result'] as $cat)
        {
            if(in_array($cat->id, $this->cat_ids))
            {
                $key = array_search($cat->id, $for_add);
                unset($for_add[$key]);
            }
            else
            {
                $for_remove[] = $cat->id;
            }
        }

        // для удаления
        foreach ($for_remove as $cat_id)
        {
            $db->where('page_id', $this->id);
            $db->where('cat_id', $cat_id);
            $db->delete('cat_pages');
        }

        // для добавления
        foreach ($for_add as $cat_id)
        {
            $data = [
                'page_id' => intval($this->id),
                'cat_id'  => intval($cat_id)
            ];

            $db->insert('cat_pages', $data);
        }

        // get page from database
        return self::getById($this->id);
    }

    public function insert()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $pages = self::_getListBy( $this->link, 'link');
        if($pages && $pages['count'] !== 0)
           return NULL;

        $data =
            [
                'title'         => $db->escape($this->title),
                'tag'           => $db->escape($this->tag),
                'author'        => $db->escape($this->author_id),
                'link'          => $db->escape($this->link),
                'short_text'    => $db->escape($this->short_text),
                'full_text'     => $db->escape($this->full_text),
                'cut_type'      => $db->escape($this->cut_type),
                'meta_title'    => $db->escape($this->meta['title']),
                'meta_desc'     => $db->escape($this->meta['description']),
                'comments'      => 0,
                'views'         => 0
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

        // get page from database
        return self::getById($page_id);
    }
}