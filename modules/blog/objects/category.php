<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class cs_cat
{
    public $id          = NULL;
    public $name        = NULL;
    public $link        = NULL;
    public $description = NULL;

    function __construct(array $data = NULL, array $needle = NULL)
    {
        if(!$data) return;

        // all basic data
        if (isset($data['id']) && (!$needle || in_array('id', $needle)))
            $this->id = (int)$data['id'];
        if (isset($data['name']) && (!$needle || in_array('name', $needle)))
            $this->name = (string)$data['name'];
        if (isset($data['link']) && (!$needle || in_array('link', $needle)))
            $this->link = (string)$data['link'];
        if (isset($data['description']) && (!$needle || in_array('description', $needle)))
            $this->description = (string)$data['description'];
    }

    protected static function _getBy($sel = FALSE, ?string $by = 'id', array $needle = NULL)
    {
        global $CS;

        if(!$by || !$sel) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where($db->escape($by), $db->escape($sel));

        if($data = $db->getOne('categories', $needle))
            return new cs_cat($data, $needle);

        return NULL;
    }

    public static function getById(?int $id, array $needle = NULL)
    {
        if(!$id) return NULL;
        $id = cs_filter($id, 'int');

        return self::_getBy($id, 'id', $needle);
    }

    public static function getListAll(cs_pagination $pagination = NULL, array $needle = NULL, $reverse = FALSE)
    {
        return self::getListBy(FALSE, FALSE, $pagination, $needle, $reverse);
    }

    public static function getListBy($sel = FALSE, ?string $by = 'id', cs_pagination $pagination = NULL, array $needle = NULL, $reverse = FALSE)
    {
        global $CS;

        if(!$db = $CS->gc('mysqli_db_helper', 'helpers')->getInstance())
            die('[blog] Can`t connect to database');

        if($reverse)
            $db->orderBy('id', 'desc');
        if($by && is_string($by) && $sel)
            $db->where($db->escape($by), $db->escape($sel));

        if(!$pagination)
        {
            if(!$objects = $db->get('categories'))
                return NULL;
        } else
        {
            $db->pageLimit = $pagination->getLimit();
            $objects = $db->arraybuilder()->paginate("categories", $pagination->getCurrentPage());
            $pagination->setTotal($db->totalCount);
            if(!$objects)
                return NULL;
        }

        $result = [];
        foreach ($objects as $item)
            $result[] = new cs_cat($item, $needle);

        return
            [
                'count'      =>  count($result),
                'result'     =>  $result
            ];
    }

    public static function getByLink(?string $link = NULL, array $needle = NULL)
    {
        $link = cs_filter($link, 'base');
        if(!$link || !is_string($link)) return NULL;
        return self::_getBy($link, 'link', $needle);
    }

    public static function getByPageId(? int $id = NULL, array $needle = NULL)
    {
        global $CS;

        $id = cs_filter($id, 'int');
        if(!$id) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where('page_id', $id);

        if(is_array($p = $db->get('cat_pages')))
        {
            $r = [];
            foreach ($p as $item)
                $r[] = $item['cat_id'];
            return self::getByIds($r, $needle);
        }
        else return NULL;
    }

    public static function getByIds(array $ids = NULL, array $needle = NULL)
    {
        global $CS;

        if(!$ids || !is_array($ids)) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where('id', $ids, 'IN');

        if(!$objects = $db->get('categories'))
            return FALSE;

        $result = [];
        foreach ($objects as $item)
            $result[] = new cs_cat($item, $needle);

        return
        [
            'count'   => count($result),
            'result'  => $result
        ];
    }

    public function insert()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $cats = self::getListBy('link', $this->link);
        if($cats !== FALSE && $cats['count'] !== 0)
            return NULL;

        $data =
            [
                'name'          => $db->escape($this->name),
                'link'          => $db->escape($this->link),
                'description'   => $db->escape($this->description),
            ];

        // function returned current cat id
        $id = $db->insert('categories', $data);

        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }
}