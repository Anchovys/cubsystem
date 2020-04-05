<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class cs_cat
{
    public $id          = NULL;
    public $name        = NULL;
    public $link        = NULL;
    public $description = NULL;

    function __construct($data = [])
    {
        if(is_array($data))
        {
            // all basic data
            if (isset($data['id']))             $this->id           = (int)$data['id'];
            if (isset($data['name']))           $this->name         = (string)$data['name'];
            if (isset($data['link']))           $this->link         = (string)$data['link'];
            if (isset($data['description']))    $this->description  = (string)$data['description'];
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

        if($data = $db->getOne('categories'))
            return new cs_cat($data);

        return NULL;
    }

    public static function getByPageId($id = FALSE)
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
            return self::getByIds($r);
        }
        else return NULL;
    }

    public static function getByIds($ids = [])
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
            $result[] = new cs_cat($item);

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