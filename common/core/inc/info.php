<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class CsInfo
{
    // for singleton
    private static $_instance = NULL;

    /**
     * @return CsInfo
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsInfo();

        return self::$_instance;
    }

    private ?array $info = [];

    /**
     * Устанавливает значение по ключу
     * @param $key string|int  - ключ, в который нужно установить значение.
     * @param $value - само значение
     */
    public function setOption($key, $value)
    {
        $this->info[$key] = $value;
    }

    /**
     * Удаляет все значения по ключу
     * @param $key - ключ
     */
    public function unsetOption($key)
    {
        unset($this->info[$key]);
        $this->info[$key] = NULL;
    }

    /**
     * Получает значение по ключу
     * @param $key - имя ключа.
     * @return bool
     */
    public function getOption($key)
    {
        if(is_array($key))
        {
            $t = $this->info;
            foreach ($key as $k)
                $t = $t[$k];
            return $t;
        }
        else return !key_exists($key, $this->info) ? FALSE :
            $this->info[$key];
    }

    /**
     * Вернуть всю конфигурацию
     * @return mixed
     */
    public function getAll()
    {
        return $this->info;
    }
}