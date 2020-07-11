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
    private static ?CsInfo $_instance = NULL;

    /**
     * @return CsInfo
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsInfo();

        return self::$_instance;
    }

    private array $info = [];
    private array $lock = [];

    /**
     * Устанавливает значение по ключу
     * @param $key string|int  - ключ, в который нужно установить значение.
     * @param $value - само значение
     * @param bool $defineType - константный тип (нельзя изменять)
     */
    public function setOption($key, $value, $defineType = FALSE)
    {
        // залочено
        if(in_array($key, $this->lock))
            return; // выходим

        $this->info[$key] = $value;

        // вносим в массив локов
        if($defineType) $this->lock[] = $key;
    }

    /**
     * Удаляет все значения по ключу
     * @param $key - ключ
     */
    public function unsetOption($key)
    {
        $this->info[$key] = NULL;
        unset($this->info[$key]);
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

    /**
     * Проверяет возможность изменения ключа
     * @param $key - ключ, по которому искать
     * @return bool
     */
    public function isDefine($key)
    {
        return in_array($key, $this->lock);
    }
}