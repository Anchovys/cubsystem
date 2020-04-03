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
        global $CS;

        if(!$id) return NULL;

        $id = intval($id);

        if(!$db = $CS->database->getInstance())
            die('[blog] Can`t connect to database');

        $db->where('id', $id);

        if($data = $db->getOne('categories'))
            return new cs_cat($data);

        return NULL;
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