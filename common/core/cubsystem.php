<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

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

class Cubsystem
{
    public ?CsInfo    $info;
    public ?CsConfig  $config;
    public ?CsHooks   $hooks;
    public ?CsHelpers $helpers;
    public ?CsSession $session;
    public ?CsRouter  $router;
    public ?CsShared  $shared;
    public ?modules_helper $modules;
    public ?template_helper $template;

    // for singleton
    private static ?Cubsystem $_instance = NULL;

    /**
     * @return Cubsystem
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new Cubsystem();

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

        // функции для дебага и отладки
        require_once(CS_COREINCPATH . 'debug.php');

        // доп функции для безопасности системы
        require_once(CS_COREINCPATH . 'security.php');

        // сессия и работа с ней
        require_once(CS_COREINCPATH . 'session.php');
        $this->session = CsSession::getInstance();
        $this->session->init();

        // доп функции для файловой системы
        require_once(CS_COREINCPATH . 'filesystem.php');

        // доп функции для url
        require_once(CS_COREINCPATH . 'url.php');

        // роуты
        require_once(CS_COREINCPATH . 'router.php');
        $this->router = CsRouter::getInstance();

        // работа с инфой
        require_once(CS_COREINCPATH . 'info.php');
        $this->info = CsInfo::getInstance();

        /* установим какую-то начальную инфу */
        $this->info->setOption('start_time', $_time);

        $this->info->setOption('system', [
            'version' => '0.10'
        ]);

        $this->info->setOption('baseurl',  CsUrl::baseUrl());
        $this->info->setOption('fullurl',  CsUrl::fullUrl());
        $this->info->setOption('segments', CsUrl::segment());

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

        // работа с хелперами
        require_once(CS_COREINCPATH . 'helpers.php');
        $this->helpers = CsHelpers::getInstance();
    }

    /**
     * Запуск системы
     * Включение всех фунций системы
     */
    public function start()
    {
        /* Хук при старте */
        $this->hooks->here('system_start');

        // первоначально определили, что система не установлена.
        if(!CsFS::fileExists(CS_CONFIGPATH . 'default.cfg.php'))
        {
            // загрузим специальный хелпер, который отвечает за установку
            $install_helper = $this->helpers->loadOnce('install');
            if($install_helper !== NULL) // загружен
                $install_helper->init(); // по дефолту вызываемая ф-я init
        }

        /* Хук до загрузки маршрутов */
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
            $this->modules->initFor(CsFS::getDirectories(CS_MODULESCPATH, FALSE));
            $this->modules->loadFor($modules_config['autoload']);
            $this->modules->loadFromData(); // юзерские модули
        }

        /* Хук до загрузки роутов */
        $this->hooks->here('system_load_router');
        $this->router->run(); // запускаем все машруты

        /* Хук до загрузки шаблона */
        $this->hooks->here('system_load_tmpl');

                    //**////////////////////
                    ///   Шаблон сайта   ///
                    ////////////////////////
        $templates_config = $this->config->getOption('template');
        if($templates_config['enabled'] === TRUE && !$this->info->getOption('in_admin'))
        {
            $template = $this->helpers->getLoaded('template');

            if($template !== NULL)
                $this->template = $template->register($templates_config['default_tmpl']);
        }

        /* Хук до вывода шаблона */
        $this->hooks->here('system_print_tmpl');

                    //**////////////////////
                    ///   Вывод шаблона  ///
                    ////////////////////////
        if($this->template !== NULL && $this->template instanceof template_helper)
            $this->template->showBuffer(0); // show main buffer

        $this->hooks->here('system_end');
    }
}