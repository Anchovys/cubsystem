<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| blog.php [rev 1.0], Назначение: поддержка статей (блога)
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

class blog_helper
{
    public $callback = ''; // file for callback
    
    public function loadPageBy($by = false, $value = false, $callback = false)
    {     
        global $CS;

        if(!$db = $CS->gc('mysqli_db_helper')->getInstance())
                    die("[blog] Can`t connect to database");
        
        $db->where($by, $value);
        
        if(!$data = $db->getOne("pages"))
            return;

        if(!file_exists($f = $callback ? $callback : $this->callback))
            die('[blog] Can`t load template callback file');

        // return as buffer output
        return cs_return_output($f, $data);
    }

    public function getPagesBy($by = false, $value = false, $count = false)
    {
        global $CS;

        if(!$db = $CS->gc('mysqli_db_helper')->getInstance())
            die("[blog] Can`t connect to database");

        if($by && $value)
            $db->where($by, $value);
        
        $result = $db->get("pages");
        
        return $count ? count($result) : $result;
    }

    public function load404Page($callback = false)
    {     
        $data = [
            'comments'      => 0,
            'author'        => "",
            'views'         => 0,
            'link'          => "",
            'title'         => "Страница не найдена!",
            'context'       => "404 Страница не найдена!"
        ];

        if(!file_exists($f = $callback ? $callback : $this->callback))
            die('[blog] Can`t load template callback file');

        return cs_return_output($f, $data);
    }
}
?>