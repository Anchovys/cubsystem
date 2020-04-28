<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| loader.php [rev 1.0], Назначение: хелпер загрузки модулей и классов
| -------------------------------------------------------------------------
| Основные методы, которые используются для загрузки модулей.
| Также сюда внедрены методы загрузки классов, помимо модулей.
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class loader_helper {

    /**
     * Подгружает все модули в массиве
     * @param $list - массив имен файлов, в папке modules
     * 
     * @return mixed
    */
    public function mod_load_for ($list = [])
    {
        $mods = [];
        if(!$list || !is_array($list)) 
            return false;

        foreach($list as $item) 
        {
            $obj = $this->mod_load($item, []);
            if($obj) $mods[$obj[0]] = $obj[1];
        }

        return $mods;
    }
    /**
     * Подгружает указанный модуль.
     * Имя класса для модуля с именем test должно иметь вид 'test_module'
     * @param $name - название модуля
     * @param $args - массив аргументов, которые будут переданы
     * 
     * @return Boolean
    */
    public function mod_load ($name = '', $args = [])
    {
        if(!$name = trim($name))
            return false;

        if(preg_match("/^\w+$/i", $name) && 
           file_exists($f = CS_MODULESCPATH . $name . _DS . $name . '.php'))
        {
            include_once($f);

            $name = $name . '_module';

            if(!class_exists($name))
                return FALSE;

            $class = new $name($args);

            if($class->config && key_exists('autoload', $class->config) && $class->config['autoload'] === TRUE)
                $class->onLoad();

            return [$name, $module = $class];
		}

        return false; 
    }
}