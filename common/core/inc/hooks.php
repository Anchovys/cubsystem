<?php defined('CS__BASEPATH') or exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
+ -------------------------------------------------------------------------
| hooks.php [rev 1.0], Назначение: системные хуки
+ -------------------------------------------------------------------------
|
| Класс реализует механизмы хуков.
|
*/

class CsHooks
{
    // for singleton
    private static ?CsHooks $_instance = NULL;

    public static function getInstance() : CsHooks
    {
        if (self::$_instance == NULL)
        {
            self::$_instance = new CsHooks();

            // сразу после загрузки хук
            self::$_instance->here('hooks_alive');
        }


        return self::$_instance;
    }

    private bool $_enabled;
    private array $_hooks = [];

    
    function __construct()
    {
        $this->_enabled = CubSystem::getInstance()->config->getOption(['hooks', 'enabled']);
        define("ANONFUNCNAME", 'anonymous_');
    }

    /**
     * Регистрирует функцию на нужный хук
     * @param $hook - название хука
     * @param $func - название функции (строка) или анонимная функция
     * @param $class - объект класса, в котором нужно вызвать
     * @param $priority - приоритет выполнения (сначала с более высокими)
     * Если мы используем анонимную функцию, то вместо class можно указать priority
     * @return bool
     */
    public function register(string $hook, $func, object $class = null, int $priority = 10)
    {
        // хук и функция должна быть указана
        if ( !$hook || !$func)
            return FALSE;

        if ($this->_enabled != TRUE)
            return FALSE;

        // какие-то дополнительные данные
        $data = '';

        // фильтр для значения приоритета
        $priority = (int)$priority;
        if ($priority < 0) $priority = 0;

        if (is_string($func))    // в качестве функции передана строка
        {
            // проверка на наличие указанной функции в классе
            if ( !$class && !function_exists($func) ||
                 $class && !method_exists($class, $func))
                return FALSE;

        } else if (is_callable($func)) // при передачи анонимной функции
        {
            // передадим функцию
            $data = $func;

            // установим такое имя
            $func = ANONFUNCNAME;

            // если есть класс
            if ($class)
            {
                $priority = $class; // используем как приоритет
                $priority = (int)$priority; // фильтр значений
                if ($priority < 0) $priority = 0;
                $class = NULL; // класс для анонмной функции не нужен
            }
        }

        // формируем имя
        $hook_name = $func;

        // уникальное имя с учетом хеша обьекта
        if (is_object($class))
            $hook_name .= spl_object_hash( (object)$class) . spl_object_hash( (object)$data);

        // делаем уникальный ID для названия
        // предположим, если добавляется несколько
        // анонимных функций из одного класса
        // (только если тип Хука анонимный)
        if($func == ANONFUNCNAME)
            $hook_name = uniqid($hook_name);

        // добавим в хуки
        $this->_hooks[$hook][$hook_name] = [$func, $priority, $class, $data];

        // сортируем элементы в порядке убывания
        arsort($this->_hooks[$hook]);

        return TRUE;
    }

    /**
     * Выполняет все хуки с указанным именем в этом месте
     * @param string $hook - название хука
     * @param array $args - те аргументы, которые нужно применить при подключении функции
     * При неудачном выполнении, вернет FALSE
     * Если хук что-то возвращает, будет возвращено последнее значение хука
     * @param int $minPriority - минимальный приоритет для отображения
     * @return Boolean
     */
    public function here(string $hook, array $args = [], int $minPriority = 0)
    {
        if ($this->_enabled != TRUE)
            return FALSE;

        // хук должен быть указан и находится в массиве
        if ( !is_string($hook))
            return FALSE;

        $arr = array_keys($this->_hooks);

        // хук есть
        if (!in_array($hook, $arr))
            return FALSE;

        // данные, которые вернем позже
        $data = TRUE;

        // перебор всех хуков с нужным именем
        foreach ($this->_hooks[$hook] as $key => $val)
        {
            $func = $val[0];
            $priority = $val[1];    // приоритет
            $class = $val[2];       // получаем класс

            // если есть отсев по приоритету
            if($priority < $minPriority)
                break;

            //доп параметр
            $data = NULL;

            // укажем доп параметр (если передан)
            if (count($val) == 3)
                $data = $val[2];

            // анонимная функция обозначается так: anonymous_
            if ($func == ANONFUNCNAME)
            {
                if($data != NULL && is_callable($data))
                    call_user_func($data);
                else if(!$data && count($val) == 4)
                {
                    // значит в 3 арге лежит Closure Object
                    if(is_object($data = $val[3])) // это так?
                        $data->__invoke(); // тогда вызываем
                }

            } else if (is_string($func)) // стандартный вызов
            {
                // при отсутствии класса, просто вызовем функцию
                if ( !$class && function_exists($func))
                    $data = $func($args);
                 // класс указан, значит ищем в нем функцию и вызываем
                else if ($class && method_exists($class, $func))
                    $data = $class->$func($args);

            } else break; // битый хук?

        }
        return $data;
    }


    /**
     * Проверяет есть ли хук в списке
     * @param $hook - название хука в системе
     * @return Boolean
     */
    public function exists(string $hook = '')
    {
        if ($this->_enabled != TRUE)
            return FALSE;

        // хук должен быть указан
        if (!is_string($hook))
            return FALSE;

        $arr = array_keys($this->_hooks);

        return in_array($hook, $arr);
    }

    /**
     * Удаление функции из хука
     * если функция не указана, то удаляются все функции из хука
     * @param string $hook - название хука
     * @param string $func - название функции
     * @return Bool
     */
    public function remove(string $hook = '', string $func = '')
    {
        if ($this->_enabled != TRUE)
            return FALSE;

        //хук должен быть указан
        if (!is_string($hook))
            return FALSE;

        $arr = array_keys($this->_hooks);

        // проверка наличия хука
        if (!in_array($hook, $arr))
            return FALSE;

        if (!$func) unset($this->_hooks[$hook]); // удалить весь хук
        else unset($this->_hooks[$hook][$func]); // часть

        return TRUE;
    }
}