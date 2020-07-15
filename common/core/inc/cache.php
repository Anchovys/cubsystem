<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class CsCache
{
    // for singleton
    private static ?CsCache $_instance = NULL;

    /**
     * @return CsCache
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsCache();

        return self::$_instance;
    }

    private ?string $_path = '';
    private ?string $_secretKey = '';
    private ?array  $_history = [];
    private ?string $_ext = 'txt';

    /**
     * @param string $directory - можно указать директорию, где будет нужен кеш
     * @param string $secret_key - можно указать секретный ключ
     * @param string $ext - можно указать расширение файла
     */
    public function init(string $directory = '', $secret_key = '', $ext = 'txt')
    {
        // получаем путь
        $path = default_val($this->_path, CS_CACHEPATH) . _DS;
        $this->_path = CsSecurity::filter($path, 'path');

        // секретный ключ учитываем
        $this->_secretKey = default_val($secret_key, CubSystem::getInstance()->config->getOption('secret_key'));

        // расширение
        $this->_ext = $ext;
    }

    public function get(string $key, bool $useHistory = FALSE, bool $keepInHistory = FALSE)
    {
        $filename = '';

        if($useHistory && array_key_exists($key, $this->_history))
        {
            $keepInHistory = FALSE;
            $data = $this->_history[$key];
        } else {

            // не получается сохранить - нет директории
            if(!CsFS::dirExists($this->_path)) return FALSE;

            $filename = md5($key . $this->_secretKey) . '.' . trim($this->_ext);
            $filename = $this->_path . $filename;
            if(!CsFS::fileExists($filename)) return FALSE;

            $data = file_get_contents($filename);
            $data = unserialize($data);
        }

        if(!is_array($data)) return FALSE;

        if($data['expire'] < time())
        {
            unlink($filename);
            return FALSE;
        }

        if($keepInHistory) $this->_history[$key] = $data;

        return unserialize($data['value']);

    }

    public function set(string $key, $value, int $time = 120, bool $keepInHistory = FALSE)
    {
        $data = [
            'value' => serialize($value),
            'expire' => time() + $time
        ];

        // не получается сохранить - нет директории
        if(!CsFS::dirExists($this->_path)) return;

        $filename = md5($key . $this->_secretKey) . '.txt';

        file_put_contents($this->_path . $filename, serialize($data));

        // сохраним в истории если хотим запомнить
        if($keepInHistory) $this->_history[$key] = $data;
    }
}