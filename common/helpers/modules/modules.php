<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class modules_helper
{
    // for singleton
    private static ?modules_helper $_instance = NULL;

    /**
     * @return modules_helper
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new modules_helper();

        return self::$_instance;
    }

    public function __construct()
    {
        require_once(CS_HELPERSPATH . 'modules' . _DS . 'module.php');
    }

    private array $_loaded = [];


    /**
     * Иницилизирует указанный массив модулей.
     * @param array $names
     * @return object[]
     */
    public function initFor(array $names)
    {
        $return = [];
        foreach ($names as $key => $name)
        {
            $return[$name] = $this->initOnce($name);
        }
        return $return;
    }

    public function initForDir(string $directory = '')
    {
        $directory = default_val($directory, CS_MODULESCPATH);
        $directory = CsSecurity::filter($directory, 'path');
        $directories = CsFS::getDirectories($directory, 0);
        foreach ($directories as $dir)
        {
            $dir = str_replace($directory, '', $dir);
            $this->initOnce($dir);
        }
    }

    function initOnce(string $name = '')
    {
        $CS = CubSystem::getInstance();

        $name = trim($name);
        if (!$name || !preg_match("/^\w+$/i", $name))
            return NULL;

        // в загруженных уже есть
        if (array_key_exists($name, $this->_loaded))
            return NULL;

        $directory = CS_MODULESCPATH . $name . _DS;
        $config = NULL;

        try {
            // подключаем файл личной конфигурации
            require_once($directory . 'config.php');
        } catch (Error $error) { $CS->errors->handleException($error); return NULL; } // maybe parse error

        // нет конфигурации
        if (empty($config) || !is_array($config))
            return NULL;

        // конфигурация задана некорректно
        if (!isset($config['enable'], $config['min_rev']))
            return NULL;

        // проверим конфигурацию
        if ($config['enable'] === FALSE ||
            (double)$config['min_rev'] > $CS->info->getOption('system')['version'])
            return NULL;

        try {
            // подключаем файл модуля
            require_once($directory . "$name.php");
        } catch (Error $error) { $CS->errors->handleException($error); return NULL; } // maybe parse error

        // проверить наличие класса
        $full_name = 'module_' . $name;
        if (!class_exists($full_name))
            return NULL;

        $this->_loaded[$name] =
            [
                'config' => $config,
                'directory' => $directory,
                'full_name' => $full_name
            ];

        return $this->_loaded[$name];
    }

    /**
     * Подгружает указанный массив модулей.
     * @param array $names
     * @return object[]
     */
    public function loadFor(array $names)
    {
        $return = [];

        foreach ($names as $key => $name)
          $return[$name] = $this->loadOnce($name);

        return $return;
    }

    public function loadForData()
    {
        $jsonData = CubSystem::getInstance()->shared->
        getTextData('modules', TRUE);
        if(is_string($jsonData) != TRUE)
            return;

        $this->loadFor(json_decode($jsonData, true));
    }

    /**
     * Выгружает все модули
     */
    public function unloadAll()
    {
        foreach ($this->_loaded as $key => $name)
        {
            $this->unloadOnce($key);
        }
    }

    function loadOnce(string $name = '')
    {
        $module_item = $this->getLoaded($name, false, false);

        if($module_item == NULL)
            return NULL;

        // по умолчанию ничего не загружено
        $config = NULL;
        $full_name = NULL;
        $directory = NULL;

        // не загружен, т.к не обьект
        if (!is_object($module_item) && is_array($module_item))
        {
            $config = $module_item['config'];
            $full_name = $module_item['full_name'];
            $directory = $module_item['directory'];
        } elseif (is_object($module_item))
        {
            // больше ничего не надо делать
            // модуль уже загружен
            if ($module_item->isLoaded === TRUE)
                return NULL;
            $config = $module_item->config;
            $full_name = $module_item->classname;
            $directory = $module_item->directory;
        } else return NULL;

        // что-то не указано
        if ($config === NULL || $full_name === NULL || $directory === NULL)
            return NULL;

        // создаем экземпляр
        $module = new $full_name($config, $directory, $full_name);

        // не подкласс модуля
        if (!is_subclass_of($module, 'CsModule'))
            return NULL;

        // нет метода "при загрузке"
        if (!method_exists($module, 'onLoad'))
            return NULL;

        // попытка загрузки
        // в процессе загрузки модуля что-то пошло не так
        if ($module->onLoad() !== TRUE)
            return NULL;

        // добавим в загруженные
        $this->_loaded[$name] = $module;

        // уже загружем, вернем экземпляр
        return $module;
    }

    public function unloadOnce(string $name = '')
    {
        $module_item = $this->getLoaded($name, true, false);

        if($module_item == NULL)
            return FALSE;

        // нечего делать
        if(!$module_item->isLoaded)
            return TRUE;

        // если обьект и загружен - выгружаем
        return $module_item->onUnload();
    }

    public function getLoaded(?string $key = NULL, bool $objectOnly = false, bool $loadOnly = false)
    {
        if($key != NULL)
        {
            if (!array_key_exists($key, $this->_loaded))
                return NULL;

            // берем экземпляр
            $module_item = $this->_loaded[$key];

            // не обьект, а надо
            if (!is_object($module_item) && $objectOnly)
                return NULL;

            // не загружен, а надо
            if(is_object($module_item) && !$module_item->isLoaded && $loadOnly)
                return NULL;

            return $this->_loaded[$key];
        }

        return $this->_loaded;
    }

    public function enableOnce(string $name = '')
    {
        // попытка загрузить
        if ($this->loadOnce($name) == FALSE)
            return FALSE; // что-то не так (не загрузился)

        // дальше здесь уже обьект, загруженный модуль
        $module = $this->_loaded[$name];

        // метод, отвечающий за включение модуля
        if ($module->onEnable() == FALSE)
            return FALSE; // прерываем операцию

        $CS = CubSystem::getInstance();

        $array = [];
        $data = $CS->shared->getTextData('modules', TRUE);

        if(is_string($data) == TRUE)
            $array = json_decode($data, TRUE);

        if(!in_array($name, $array) && in_array($name, $this->_loaded))
            array_push($array, $name);

        $jsonData = json_encode($array);

        $CS->shared->saveTextData('modules', $jsonData, TRUE);

        return TRUE;
    }

    public function disableOnce(string $name = '')
    {
        $module_item = $this->getLoaded($name, true, true);

        if($module_item == NULL)
            return FALSE;

        // отключаем функцией
        if ($module_item->onDisable() !== TRUE)
            return FALSE; // не выгрузился

        // попытка выгрузить
        if ($this->unloadOnce($name) !== TRUE)
            return FALSE; // что-то не так (не выгрузился)

        $CS = CubSystem::getInstance();

        $array = [];
        $data = $CS->shared->getTextData('modules', TRUE);
        if(is_string($data) == TRUE)
            $array = json_decode($data, TRUE);

        if (($key = array_search($name, $array)) !== false) {
            unset($array[$key]);
        }

        $jsonData = json_encode($array);
        $CS->shared->saveTextData('modules', $jsonData, TRUE);

        return TRUE;
    }

    public function purgeOnce(string $name = '')
    {
        $module_item = $this->getLoaded($name, true, false);

        if($module_item == NULL)
            return FALSE;

        // что-то чистим
        if ($module_item->onPurge() !== TRUE)
            return FALSE; // не очистил

        // отключаем функцией
        if ($this->disableOnce($name) !== TRUE)
            return FALSE; // не выгрузился

        return TRUE;
    }
}
