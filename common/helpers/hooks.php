<?php
defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| hooks_helper.php [rev 1.1], Назначение: внедрение функциональности хуков
| -------------------------------------------------------------------------
| В этом файле описана базовая функциональность хуков, с помощью которой
| можно изменить код программы и внедрить свой функционал.
| Используется для создания модулей.
|
|
@
@   Cubsystem CMS, (с) 2019
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class hooks_helper {

    /**
     * Регистрирует функцию на нужный хук
     * @param $hook - название хука
     * @param $func - название функции (строка) или анонимная функция
     * @param $class - объект класса, в котором нужно вызвать
     * @param $priority - приоритет выполнения (сначала с более высокими)
     * Если мы используем анонимную функцию, то вместо class можно указать priority
     * @return Not
    */
    public function register($hook = '', $func = '', $class = '', $priority = 10)
    {
        global $CS;

        //хук и функция должна быть указана
        if (!$hook || !$func)
        {
            return false;
        }

        //хуки отключены?
        if(!$CS->config['enable_hooks']) 
        {
            return false;
        }

        //фильтр для значения приоритета
        $priority = Filter($priority, 'int;ab_zero');

        if(is_string($func))    //в качестве функции передана строка
        {
            //проверка на наличие указанной функции в классе
            if(!$class)
            {
                if ( !function_exists($func) ) 
                {
                    return false;
                }
            }
            else
            {
                if( !method_exists($class, $func) )
                {
                    return false;
                }
            }

        }

        //какие-то дополнительные данные
        $data = '';

        //при передачи анонимной функции
        if(is_callable($func)) 
        {
            //передадим функцию
            $data = $func;

            //установим такое имя
            $func = 'anonymous_';

            //если есть класс
            if($class)
            {
                $priority = $class; //используем как приоритет
                $priority = Filter($priority, 'int;ab_zero'); //фильтр значений
                $class = null; //класс для анонмной функции не нужен
            }
        }

        //добавим в хуки
        $CS->hooks[$hook][$func] = array($priority, $class, $data);

        //сортируем элементы в порядке убывания
        arsort($CS->hooks[$hook]);
        
    }

    /**
     * Выполняет все хуки с указанным именем в этом месте
     * @param $hook - название хука
     * @param $args - те аргументы, которые нужно применить при подключении функции
     * При неудачном выполнении, вернет false
     * @return Boolean
    */
    public function here($hook = '', $args = array())
    {
        global $CS;

        //хук должен быть указан
        if ($hook && $CS->config['enable_hooks']) 
        {
            $arr = array_keys($CS->hooks);
            
            //хук есть
            if ( in_array($hook, $arr) )
            {
                //перебор всех хуков с нужным именем
                foreach ( $CS->hooks[$hook] as $func => $val)
                {
                    //получаем класс
                    $class = $val[1];

                    //доп параметр
                    $data = '';

                    //укажем доп параметр (если передан)
                    if(count($val) == 3)
                    {
                        $data = $val[2];
                    }

                    //анонимная функция обозначается так: anonymous_
                    if($data && $func == 'anonymous_' &&  is_callable($data)) 
                    {
                        call_user_func($data);
                    }
                    else if(is_string($func)) //стандартный вызов
                    {
                        if(!$class) //при отсутствии класса, просто вызовем функцию
                        {
                            if ( function_exists($func) ) 
                            {
                                $func($args);
                            }
                        }
                        else //класс указан, значит ищем в нем функцию и вызываем
                        {
                            
                            if( method_exists($class, $func) ) 
                            {
                                $class->$func($args);
                            }
                        }
                    } else //что-то другое...
                    {
                        return false; 
                    }
                    
                }
                return true;
            }
        }

        return false;
        
    }


    /**
     * Проверяет есть ли хук в списке
     * @param $hook - название хука в системе
     * @return Boolean
    */
    public function exists($hook = '')
    {
        global $CS;

        if ($hook && $CS->config['enable_hooks']) 
        {
            $arr = array_keys($CS->hooks);

            if ( in_array($hook, $arr) ) 
            {
                return true;
            }
        }

        return false;
    }

    /**
    * Удаление функции из хука
    * если функция не указана, то удаляются все функции из хука
     * @param $hook - название хука
     * @param $func - название функции
     * @return Bool
    */
    public function remove($hook = '', $func = '')
    {
        global $CS;

        //хук должен быть указан и хуки включены
        if ($hook && $CS->config['enable_hooks']) 
        {
            $arr = array_keys($CS->hooks);

            //проверка наличия хука
            if ( in_array($hook, $arr) )
            {
                if (!$func) // удалить весь хук
                {
                    unset($CS->hooks[$hook]);
                }
                else
                {
                    unset($CS->hooks[$hook][$func]);
                }

                return true;
            }
        }
        return false;
    }
}