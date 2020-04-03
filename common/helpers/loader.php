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
            return class_exists($name) ? [$name, $module = new $name($this)] : false;
		}

        return false; 
    }


    /**
     * Подгружает все классы в массиве
     * @param $list - массив имен классов или массив вида:
     * имя класса (str), autojoin(bool).
     * 
     * @return No
    */
    public function class_load_for ($list = array())
    {
        
        if(!$list || !is_array($list))
        {
            return false;
        }

        foreach($list as $item) 
        {
            $autojoin = false;
            
            //подразумеваем, что может прийти не только название класса, но
            //и массив вида array (имя класса (str), autojoin(bool)).
            if(is_array($item)) {
                $item       = $item[0];
                $autojoin   = $item[1];
            }
            //загружаем
            $this->class_load($item, $autojoin);
        }
    }

    /**
     * Подгружает указанный класс
     * 
     * Также может сработать автоподключение, если autojoin true. 
     * подключенный класс будет помещен в массив classes
     * 
     * @param $classname - название класса
     * @param $autojoin - указывает, добавить ли экземпляр класса в массив classes
     * 
     * ! имя класса должно совпадать с именем файла, иначе не будет выполнено autojoin
     * ! классы с одинаковым именем не будут загружены автозагрузчиком
     * 
     * @return Boolean
    */
    public function class_load ($classname = '', $autojoin = true)
    {
        if(!$classname) 
        {
            return false;
        }

        global $CS;

        if(preg_match("/^\w+$/i", $classname) && 
            file_exists($CS->config['classes_dir'] . $classname . ".class.php")) 
        {
            //подключим класс
            include_once($CS->config['classes_dir'] . $classname . ".class.php");

            $fullClassname = $classname . "_class";
         
            //выполним автоподключение, если это необходимо
            if($autojoin) {
    
                if(class_exists($fullClassname)) {
    
                    //дубли не нужны
                    if(!array_key_exists($classname, $CS->classes))
                    {
                        $CS->classes[$classname] = new $fullClassname();
                        return true;
                    }
                } 
            }
        }
        return false;
    }
}
?>