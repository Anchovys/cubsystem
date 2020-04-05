<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class cs_cat
{
    public $id          = NULL;
    public $name        = NULL;
    public $link        = NULL;
    public $description = NULL;

    function __construct($data = [], $needle = FALSE)
    {
        if(is_array($data))
        {
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
    }

    public static function getById($id = FALSE, $needle = FALSE)
    {
        $id = cs_filter($id, 'int');
        if(!$id) return NULL;
        return self::getBy($id, 'id', $needle);
    }

    public static function getByLink($link = FALSE, $needle = FALSE)
    {
        $link = cs_filter($link, 'base');
        if(!$link || !is_string($link)) return NULL;
        return self::getBy($link, 'link', $needle);
    }

    private static function getBy($sel = FALSE, $by = 'id', $needle = FALSE)
    {
        global $CS;

        if(!$by || !$sel) return NULL;

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where($by, $sel);

        if($data = $db->getOne('categories'))
            return new cs_cat($data, $needle);

        return NULL;
    }

    public static function getByPageId($id = FALSE, $needle = FALSE)
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

    public static function getByIds($ids = [], $needle = FALSE)
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

        /*
        $cats = self::getListBy('link', $this->link);
        if($cats !== FALSE && $cats['count'] !== 0)
            return NULL;
        */

        $data = [
            'name'          => $this->name,
            'link'          => $this->link,
            'description'   => $this->description,
        ];

        // function returned current cat id
        $id = $db->insert('categories', $data);


        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }
}
?>