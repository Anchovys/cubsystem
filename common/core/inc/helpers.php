<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class CsHelpers
{
    // for singleton
    private static ?CsHelpers $_instance = NULL;

    /**
     * @return CsHelpers
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsHelpers();

        return self::$_instance;
    }

    private array $_loaded = [];

    /**
     * Подгружает указанный массив хелперов.
     * @param array $names
     * @return object[]
     */
    public function loadFor(array $names)
    {
        $return = [];
        foreach ($names as $key=>$name)
        {
            $return[$name] = $this->loadOnce($name);
        }
        return $return;
    }

    /**
     * Подгружает указанный хелпер.
     * Имя класса для хелпера с именем test должно иметь вид 'test_helper'
     * @param $name - название хелпера
     * @param $args - массив аргументов, которые будут переданы
     *
     * @return object
     */
    public function loadOnce(string $name = '', ?array $args = [])
    {
        global $CS;
        $name = trim($name);

        if(!$name || !preg_match("/^\w+$/i", $name))
            return NULL;

        // есть в игноре
        if(in_array($name, $CS->config->getOption(['helpers', 'ignore'])))
            return NULL;

        $filename = CS_HELPERSPATH . $name . _DS . "$name.php";

        if(!CsFS::fileExists($filename))
            return NULL;

        try {
            // подключаем файл
            require_once($filename);
        } catch (Error $error) { return $error; } // maybe parse error

        // класс еще не загружен
        $class = NULL;

        $full_name = $name . '_helper';
        if(array_key_exists($full_name, $this->_loaded))
            return NULL;

        // класс есть
        if(class_exists($full_name))
        {
            // поддержка singleton
            if($CS->config->getOption(['helpers', 'singleton_support']) &&
                method_exists($full_name,'getInstance'))
            {
                $class = $full_name::getInstance();
            }
            else // и без singleton
                $class = new $full_name($args);
        }

        // помечаем что загрузили
        $this->_loaded[$name] = $class;

        // ок
        return $class;
    }

    public function getLoaded(?string $key = NULL)
    {
        return $key ? $this->_loaded[$key] : $this->_loaded;
    }
}