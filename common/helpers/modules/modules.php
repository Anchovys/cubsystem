<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class modules_helper
{
    // for singleton
    private static $instance = NULL;

    /**
     * @return modules_helper
     */
    public static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new modules_helper();

        return self::$instance;
    }

    public function __construct()
    {
        require_once(CS_HELPERSPATH . 'modules/module.php');
        //$this->initOnce('demo');
        //$this->loadOnce('demo');
    }

    private array $_loaded = [];

    public function loadFromData()
    {
        $jsonData = Cubsystem::getInstance()->shared->
            getTextData('modules', TRUE);

        if(is_string($jsonData) != TRUE)
            return;

        $this->loadFor(json_decode($jsonData));
    }

    /**
     * Иницилизирует указанный массив модулей.
     * @param array $names
     */
    public function initFor(array $names)
    {
        foreach ($names as $key => $name) {
            $this->initOnce($name);
        }
    }

    /**
     * Подгружает указанный массив модулей.
     * @param array $names
     */
    public function loadFor(array $names)
    {
        foreach ($names as $key => $name) {
            $this->loadOnce($name);
        }
    }

    function initOnce(string $name = '')
    {
        global $CS;

        $name = trim($name);
        if (!$name || !preg_match("/^\w+$/i", $name))
            return FALSE;

        // в загруженных уже есть
        if (array_key_exists($name, $this->_loaded))
            return FALSE;

        $directory = CS_MODULESCPATH . $name . _DS;
        $config = NULL;

        // подключаем файл личной конфигурации
        require_once($directory . 'config.php');

        // нет конфигурации
        if (empty($config) || !is_array($config))
            return FALSE;

        // конфигурация задана некорректно
        if (!isset($config['enable'], $config['min_rev']))
            return FALSE;

        // проверим конфигурацию
        if ($config['enable'] === FALSE ||
            (double)$config['min_rev'] > $CS->info->getOption('system')['version'])
            return FALSE;

        // подключаем файл модуля
        require_once($directory . "$name.php");

        // проверить наличие класса
        $full_name = 'module_' . $name;
        if (!class_exists($full_name))
            return FALSE;

        $this->_loaded[$name] =
            [
                'config' => $config,
                'directory' => $directory,
                'full_name' => $full_name
            ];

        return TRUE;
    }

    function loadOnce(string $name = '')
    {
        $module_item = $this->getLoaded($name, false, false);

        if($module_item == NULL)
            return FALSE;

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
                return FALSE;
            $config = $module_item->config;
            $full_name = $module_item->classname;
            $directory = $module_item->directory;
        } else return FALSE;

        // что-то не указано
        if ($config === NULL || $full_name === NULL || $directory === NULL)
            return FALSE;

        // создаем экземпляр
        $module = new $full_name($config, $directory, $full_name);

        // не подкласс модуля
        if (!is_subclass_of($module, 'CsModule'))
            return FALSE;

        // нет метода "при загрузке"
        if (!method_exists($module, 'onLoad'))
            return FALSE;

        // попытка загрузки
        // в процессе загрузки модуля что-то пошло не так
        if ($module->onLoad() !== TRUE)
            return FALSE;

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

        $CS = Cubsystem::getInstance();

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

        $CS = Cubsystem::getInstance();

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

        // отключаем функцией
        if ($this->disableOnce($name) !== TRUE)
            return FALSE; // не выгрузился

        // что-то чистим
        if ($module_item->onPurge() !== TRUE)
            return FALSE; // не очистил

        return TRUE;
    }
}