<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

define('CS_MODULESCPATH', CS__BASEPATH   . 'modules' . _DS);

define('CS_COREPATH',      CS_COMMONPATH  . 'core'   . _DS);
define('CS_DISTRPATH',    CS_COMMONPATH  . 'distr'   . _DS);
define('CS_CONFIGPATH',   CS_COMMONPATH  . 'config'  . _DS);
define('CS_HELPERSPATH',  CS_COMMONPATH  . 'helpers' . _DS);

define('CS_TEMPPATH',     CS_COMMONPATH  . 'temp'    . _DS);
define('CS_CACHEPATH',    CS_TEMPPATH    . 'cache'   . _DS);
define("CS_DATAPATH",     CS_TEMPPATH    . 'csdata'  . _DS);

define('CS_COREINCPATH',  CS_COREPATH    . 'inc'     . _DS);

define("CS_SHAREDPATH",   CS__BASEPATH   . 'shared'  . _DS);
define("CS_UPLOADSPATH",  CS_SHAREDPATH  . 'uploads' . _DS);

/* debug, release */
define("CS_ENV", "release");

class CubSystem
{
    public ?CsInfo    $info = NULL;
    public ?CsConfig  $config = NULL;
    public ?CsHooks   $hooks = NULL;
    public ?CsHelpers $helpers = NULL;
    public ?CsSession $session = NULL;
    public ?CsRouter  $router = NULL;
    public ?CsShared  $shared = NULL;
    public ?CsErrors  $errors = NULL;
    public ?CsCache   $cache = NULL;
    public ?ajax_helper $ajax = NULL;
    public ?admin_helper $admin = NULL;
    public ?mysql_helper $mysql = NULL;
    public ?modules_helper $modules = NULL;
    public ?template_helper $template = NULL;
    public ?authorize_helper $auth = NULL;

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
		require_once(CS_COREPATH . 'join.php');
		
		/* для статистики */
		$_time = microtime(TRUE);
		$this->errors = CsErrors::getInstance();
		$this->errors->init();
		$this->session = CsSession::getInstance();
		$this->session->init();
		$this->info = CsInfo::getInstance();

		/* установим какую-то начальную инфу */
		$this->info->setOption('start_time', $_time, TRUE);
		$this->info->setOption('system', 
		[
			'core_version' => default_val_array($cs_core, 'rev', '1.0'),
			'version' => '0.13'
		], TRUE);
		$this->info->setOption('currenturi',  CsUrl::currentUri(), TRUE);
		$this->info->setOption('baseurl',     CsUrl::baseUrl(), TRUE);
		$this->info->setOption('fullurl',     CsUrl::fullUrl(), TRUE);
		$this->info->setOption('segments',    CsUrl::segment(), TRUE);
		$this->config = CsConfig::getInstance();
		
		/* сразу подгрузим из файла */
		$this->config->fromFile('default');
		$this->cache = CsCache::getInstance();
		$this->cache->init();
		$this->shared = CsShared::getInstance();
		$this->hooks = CsHooks::getInstance();
		$this->router = CsRouter::getInstance();
		$this->router->set404(function () {
			$this->hooks->here('system_router_404');
		});
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
        if ($helpers_config['enabled'] === TRUE) {
            $this->helpers->loadFor($helpers_config['priority']);
            $this->helpers->loadFor(CsFS::getDirectories(CS_HELPERSPATH, FALSE));
        }

        /* Хук до загрузки шаблона */
        $this->hooks->here('system_load_tmpl');

        /* Хук до загрузки модулей */
        $this->hooks->here('system_load_modules');

                //**////////////////////
                /// Бинд для модулей ///
                ////////////////////////
        $modules_config = $this->config->getOption('modules');
        if($modules_config['enabled'] === TRUE)
        {
            $this->modules = $this->helpers->getLoaded('modules');

            if($this->modules !== NULL) {
                $this->modules->initForDir(CS_MODULESCPATH);
                $this->modules->loadFor($modules_config['autoload']);
                $this->modules->loadForData(); // юзерские модули
            } else throw new Exception("Modules enabled, but no helper defined.");
        }

        /* Хук после модулей */
        $this->hooks->here('system_load_modules_end');


                //**////////////////////
                ///   Шаблон сайта   ///
                ////////////////////////
        $templates_config = $this->config->getOption('template');
        if($templates_config['enabled'] === TRUE && !$this->info->getOption('ignore_default_template'))
        {
            $template = $this->helpers->getLoaded('template');
            if($template !== NULL && $template->registered == FALSE) {
                $this->template = $template->register($templates_config['default_tmpl']);
            } else throw new Exception("Template enabled, but no helper defined.");
        }

        /* Хук до выполнения роутов */
        $this->hooks->here('system_load_router');
        $this->router->run(); // запускаем все машруты
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
        {
            $this->template->showBuffer(0);
        }

        /* Хук на конец выполнения */
        $this->hooks->here('system_end');

        /* Модули: "выгружаем" все */
        $this->modules->unloadAll();

        return $buffer;
    }
}