<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/* хелпер для запуска установщика  */
class install_helper
{
    // for singleton
    private static ?install_helper $_instance = NULL;

    /**
     * @return install_helper
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new install_helper();

        return self::$_instance;
    }

    public function __construct()
    {
        $CS = CubSystem::getInstance();
        if(!$CS->info->getOption('installed'))
        {
            $this->init(); // по дефолту вызываемая ф-я init
            $CS->info->setOption('ignore_default_template', TRUE, TRUE);
        }
    }

    /* Функция запускается, когда требуется выполнить установку  */
    public function init()
    {
        if(CsUrl::segmentEquals('install', 0))
        {
            $this->install();
            die;
        }

        die("[CubSystem] Need install system <a href='".CsUrl::absUrl()."?m=install'>Go to install</a>");
    }

    /* Инициирование установки  */
    private function install()
    {
        // для apache создаем файл htaccess
        if(strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== FALSE)
            $this->makeHtaccess() or die('Can`t make .htaccess file. Please, create this file or configure directory rules!');

    }

    /* Функция создает файл htaccess  */
    private function makeHtaccess()
    {
        $htaccess_content = file_get_contents(CS_DISTRPATH . 'htaccess_distr.txt');
        $htaccess_content = str_replace('{path}', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', $htaccess_content);
        return file_put_contents(CS__BASEPATH . '.htaccess', $htaccess_content);
    }
}