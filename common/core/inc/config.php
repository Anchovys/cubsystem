<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
+ -------------------------------------------------------------------------
| config.php [rev 1.0], Назначение: управление файлами конфигурации
+ -------------------------------------------------------------------------
|
| Класс позволяет удобно управлять файлами конфигурации системы.
|
*/

class CsConfig
{
    // for singleton
    private static ?CsConfig $_instance = null;

    public static function getInstance() : CsConfig
    {
        if (self::$_instance == null)
            self::$_instance = new CsConfig();

        return self::$_instance;
    }

    private ?array $config = [];

    /**
     * Функция считывает файл, находит в нем массив в переменной $config и добавляет в
     * конфигурацию. ключ - имя файла, указанное в переменной name. Можно также и указать категорию
     * @param $name - имя файла в каталоге конфигураций. на конце .cfg.php указывать не надо
     * @return bool
     */
    public function fromFile($name)
    {
        include_once(CS_CONFIGPATH . "$name.cfg.php");

        if(!isset($config) || !is_array($config))
            return FALSE;

        $this->setOption(NULL, $config, $name);
    }

    /**
     * Устанавливает значение по ключу
     * @param $key string|int  - ключ, в который нужно установить значение.
     * @param $value - само значени
     * @param $cat - категория, по умолчанию 'default'
     */
    public function setOption($key, $value, $cat = 'default')
    {
        if(!$this->checkCat($cat))
            $this->setCat($cat);

        if($key === NULL) $this->config[$cat] = $value;
        else $this->config[$cat][$key] = $value;
    }

    /**
     * Удаляет все значения по ключу
     * @param $key - ключ
     * @param $cat - категория, по умолчанию 'default'
     * @return bool
     */
    public function unsetOption($key, $cat = 'default')
    {
        if(!$this->checkCat($cat))
            return FALSE;

        unset($this->config[$cat][$key]);
        $this->config[$cat][$key] = null;

        return TRUE;
    }

    /**
     * Регистрирует новую категорию
     * @param $cat - имя категории
     * @return bool
     */
    public function setCat($cat)
    {
        if(!$cat || $this->checkCat($cat))
            return FALSE;

        $this->config[$cat] = [];
        return TRUE;
    }

    /**
     * Проверяет, есть ли указанная категория
     * @param $cat - имя категории
     * @return bool
     */
    public function checkCat($cat)
    {
        return ($cat && array_key_exists($cat, $this->config));
    }

    /**
     * Получает значение по ключу
     * @param $key - имя ключа. можно передать массив,
     * тогда обеспечится вложенность ключей
     * @param $cat - категория
     * @return bool
     */
    public function getOption($key, $cat = 'default')
    {
        if(!$this->checkCat($cat))
            return FALSE;

        $r = $this->config[$cat];
        if(!is_array($key))
            return !array_key_exists($key, $r) ? FALSE : $r[$key];
        {
            foreach ($keys = $key as $key)
            {
                $r = $r[$key];
            }
        }
        return $r;
    }

    /**
     * Вернуть всю конфигурацию
     * @return mixed
     */
    public function getAll()
    {
        return $this->config;
    }
}