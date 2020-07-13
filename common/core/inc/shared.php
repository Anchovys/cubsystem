<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class CsShared
{
    // for singleton
    private static ?CsShared $_instance = NULL;

    /**
     * @return CsShared
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsShared();

        return self::$_instance;
    }

    /**
     *  Проверяет все нужные директории,
     *  и, если нужно, создает их
     */
    private function checkDirectories()
    {
        CsFS::mkdirIfNotExists(CS_SHAREDPATH);
        CsFS::mkdirIfNotExists(CS_UPLOADSPATH);
        CsFS::mkdirIfNotExists(CS_DATAPATH);
        CsFS::mkdirIfNotExists(CS_CACHEPATH);
    }

    /**
     * @param $key - ключ для сохранения
     * @param $data - данные для сохранеия
     * @param bool $hashName - кодировать имя хешем или нет
     * @return bool|false|int
     */
    public function saveTextData($key, $data, bool $hashName = FALSE)
    {
        $this->checkDirectories();

        $filename = CS_DATAPATH .
            CsSecurity::hash($key, $hashName, 'sha1') . '.dat';

        return $this->put($filename, $data, TRUE, TRUE);
    }

    /**
     * @param $key - ключ по которому элемент был сохранен
     * @param bool $hashName - кодировать имя хешем или нет
     * @return string
     */
    public function getTextData($key, bool $hashName = FALSE)
    {
        $this->checkDirectories();

        $filename = CS_DATAPATH .
            CsSecurity::hash($key, $hashName, 'sha1') . '.dat';

        return $this->get($filename, TRUE);
    }

    public function putCache($key, string $data)
    {
        $this->checkDirectories();

        $filename = CS_DATAPATH .
            CsSecurity::hash($key, true, 'sha1') . '.dat';

        return $this->put($filename, $data, FALSE, TRUE);
    }

    public function getCache($key)
    {
        $this->checkDirectories();

        $filename = CS_DATAPATH .
            CsSecurity::hash($key, true, 'sha1') . '.dat';

        return $this->get($filename, TRUE);
    }


    private function put(string $filename, $data, bool $toJson = TRUE, bool $replace = TRUE)
    {
        // данные в json
        $data = $toJson ? json_encode($data) : $data;

        // скипаем замену
        if(CsFS::fileExists($filename) && !$replace)
            return FALSE;

        return file_put_contents($filename, $data);
    }

    private function get(string $filename, bool $fromJson)
    {
        if(!CsFS::fileExists($filename))
            return NULL;

        $data = file_get_contents($filename);
        return $fromJson ? json_decode($data, TRUE) : $data;
    }
}