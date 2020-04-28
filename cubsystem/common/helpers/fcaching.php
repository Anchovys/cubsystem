<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| fcaching.php [rev 1.0], Назначение: управление файловым кешем
| -------------------------------------------------------------------------
|
| Хелпер позволяет удобно управлять файловым кешем
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/
class fcaching_helper
{
    private $prefix = 'cs_';
    private $ext    = '.cache';

    public function __construct()
    {
        define("CS_CACHING_DIR", CS_TEMPPATH . 'cache' . _DS);

    }

    public function setPrefix(?string $prefix)
    {
        if(is_string($prefix = trim($prefix)))
        {
            $this->prefix = $prefix;
            return TRUE;
        }
        return false;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function get(?string $key = '')
    {
        $total_path = CS_CACHING_DIR . _fn($key);

        if(!file_exists($total_path))
            return FALSE;

        $date = (int)explode('~', basename($total_path, $this->ext))[1];

        if(time() >= $date)
        {
            unset($total_path);
            return FALSE;
        }

        $data = file_get_contents($total_path);

        return json_decode($data, TRUE);
    }

    public function purge(?string $key = '')
    {
        $total_path = CS_CACHING_DIR . _fn($key);

        if(!file_exists($total_path))
            return FALSE;

        return unlink($total_path);
    }

    public function push(?string $key = '', $time = NULL, $value = FALSE, $override = FALSE)
    {
        global $CS;

        $time = $time ? $time : $CS->config['file_caching_time'];
        $total_path = CS_CACHING_DIR . _fn($key, time() + $time);

        //// huch ..

        //проверяем файловую систему
        if(!$override && file_exists($total_path) || is_writable($total_path))
            return FALSE;

        // в json
        $value = json_encode($value);

        return file_put_contents($total_path, $value);
    }

    public function clearAll()
    {
        $files = cs_in_path_files(CS_CACHING_DIR, TRUE, [ $this->ext ]);
        foreach ($files as $file)
            if(!unlink($file)) return FALSE;
    }

    private function _fn(?string $key, $expire = 0)
    {
        $key = str_replace($key, '~', '-');
        return $this->prefix . md5($key  . cs_base_url()) . '~' . $expire . $this->ext;
    }
}