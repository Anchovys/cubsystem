<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

define('CS_MODULESCPATH', CS__BASEPATH   . 'modules' . _DS);

define('CS_DISTRPATH',    CS_COMMONPATH  . 'distr'  . _DS);
define('CS_CONFIGPATH',   CS_COMMONPATH  . 'config'  . _DS);
define('CS_HELPERSPATH',  CS_COMMONPATH  . 'helpers' . _DS);

define('CS_TEMPPATH',     CS_COMMONPATH  . 'temp'    . _DS);
define('CS_CACHEPATH',    CS_TEMPPATH    . 'cache'   . _DS);
define("CS_DATAPATH",     CS_TEMPPATH    . 'csdata'  . _DS);

define('CS_COREINCPATH',  CS_COREPATH    . 'inc'     . _DS);

define("CS_SHAREDPATH",   CS__BASEPATH   . 'shared'  . _DS);
define("CS_UPLOADSPATH",  CS_SHAREDPATH  . 'uploads' . _DS);

class CubSystem
{
    public ?CsInfo    $info;
    public ?CsConfig  $config;
    public ?CsHooks   $hooks;
    public ?CsHelpers $helpers;
    public ?CsSession $session;
    public ?CsRouter  $router;
    public ?CsShared  $shared;
    public ?CsErrors  $errors;
    public ?modules_helper $modules;
    public ?template_helper $template;

    // for singleton
    private static ?CubSystem $_instance = NULL;

    /**
     * @return CubSystem
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CubSystem();

        return self::$_instance;
    }

    /**
     * Иницилизация системы
     * Включение всех файлов и т.д
     */
    public function init()
    {
        // для статистики
        $_time = microtime(TRUE);

        // дополнительные функции
        require_once(CS_COREINCPATH . 'functions.php');

        // функции статистики
        require_once(CS_COREINCPATH . 'stats.php');

        // обработчик ошибок
        require_once(CS_COREINCPATH . 'errors.php');
        $this->errors = CsErrors::getInstance();
        $this->errors->init();

        // доп функции для безопасности системы
        require_once(CS_COREINCPATH . 'security.php');

        // сессия и работа с ней
        require_once(CS_COREINCPATH . 'session.php');
        $this->session = CsSession::getInstance();
        $this->session->init();

        // доп функции для файловой системы
        require_once(CS_COREINCPATH . 'filesystem.php');

        // доп функции для работы с url
        require_once(CS_COREINCPATH . 'url.php');

        // работа с инфой
        require_once(CS_COREINCPATH . 'info.php');
        $this->info = CsInfo::getInstance();
            /* установим какую-то начальную инфу */
            $this->info->setOption('start_time', $_time, TRUE);
            $this->info->setOption('system', ['version' => '0.11'], TRUE);
            $this->info->setOption('currenturi',  CsUrl::currentUri(), TRUE);
            $this->info->setOption('baseurl',     CsUrl::baseUrl(), TRUE);
            $this->info->setOption('fullurl',     CsUrl::fullUrl(), TRUE);
            $this->info->setOption('segments',    CsUrl::segment(), TRUE);

        // работа с конфигами
        require_once(CS_COREINCPATH . 'config.php');
        $this->config = CsConfig::getInstance();
            // сразу подгрузим из файла
            $this->config->fromFile('default');

        // файлы в папке shared
        require_once(CS_COREINCPATH . 'shared.php');
        $this->shared = CsShared::getInstance();

        // работа с хуками
        require_once(CS_COREINCPATH . 'hooks.php');
        $this->hooks = CsHooks::getInstance();

        // роуты (маршруты)
        require_once(CS_COREINCPATH . 'router.php');
        $this->router = CsRouter::getInstance();
        $this->router->set404(function () {
            $this->hooks->here('system_router_404');
        });

        // работа с хелперами
        require_once(CS_COREINCPATH . 'helpers.php');
        $this->helpers = CsHelpers::getInstance();
    }

    /**
     * Запуск системы
     * Включение всех фунций системы
     * @throws Exception
     */
    public function start()
    {
        /* Хук при старте */
        $this->hooks->here('system_start');

        /* Определим, нужно ли запускать установщик */
        // первоначально определили, что система не установлена.
        $cs_installed = CsFS::fileExists(CS_CONFIGPATH . 'default.cfg.php');
        $this->info->setOption('installed', $cs_installed, TRUE);

        /* Хук до загрузки хелперов */
        $this->hooks->here('system_load_helpers');

                    //**/////////////////////
                    /// Загрузка хелперов ///
                    /////////////////////////
        $helpers_config = $this->config->getOption('helpers');
        if($helpers_config['enabled'] === TRUE)
        {
            $this->helpers->loadFor($helpers_config['priority']);
            $this->helpers->loadFor(CsFS::getDirectories(CS_HELPERSPATH, FALSE));
        }

        /* Хук до загрузки модулей */
        $this->hooks->here('system_load_modules');

                    //**////////////////////
                    /// Бинд для модулей ///
                    ////////////////////////
        $modules_config = $this->config->getOption('modules');
        if($modules_config['enabled'] === TRUE)
        {
            $this->modules = $this->helpers->getLoaded('modules');

            if($this->modules !== NULL)
            {
                $this->modules->initForDir(CS_MODULESCPATH);
                $this->modules->loadFor($modules_config['autoload']);
                $this->modules->loadForData(); // юзерские модули
            } else throw new Exception("Modules enabled, but no helper defined.");
        }

        /* Хук до выполнения роутов */
        $this->hooks->here('system_load_router');
        $this->router->run(); // запускаем все машруты

        // система не установлена
        if(!$this->info->getOption('installed'))
        {
            // загрузим специальный хелпер, который отвечает за установку
            $install_helper = $this->helpers->loadOnce('install');
            if($install_helper !== NULL) // загружен
            {
                $install_helper->init(); // по дефолту вызываемая ф-я init
                $this->info->setOption('ignore_default_template', TRUE, TRUE);
            } else throw new Exception("Error, no defined install helper.");
        }

        /* Хук до загрузки шаблона */
        $this->hooks->here('system_load_tmpl');

                    //**////////////////////
                    ///   Шаблон сайта   ///
                    ////////////////////////
        $templates_config = $this->config->getOption('template');
        if($templates_config['enabled'] === TRUE && !$this->info->getOption('ignore_default_template'))
        {
            $template = $this->helpers->getLoaded('template');
            if($template !== NULL)
                $this->template = $template->register($templates_config['default_tmpl']);
            else throw new Exception("Template enabled, but no helper defined.");
        }
    }

    /**
     * Вывод на экран
     */
    public function out()
    {
        $buffer = '';
        /* Хук до вывода шаблона */
        $this->hooks->here('system_print_tmpl');

        //**////////////////////
        ///   Вывод шаблона  ///
        ////////////////////////
        if($this->template !== NULL && $this->template instanceof template_helper)
            $this->template->showBuffer(0);

        /* Хук на конец выполнения */
        $this->hooks->here('system_end');

        /* Модули: "выгружаем" все */
        $this->modules->unloadAll();

        return $buffer;
    }
}