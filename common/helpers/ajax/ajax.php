<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
/*
+ -------------------------------------------------------------------------
| ajax.php [rev 2.0], Назначение: управление Ajax
+ -------------------------------------------------------------------------
|
| Хелпер позволяет удобно управлять Ajax и определять свои обработчики
| Взаимодействие с хелпером очень простое
|  1. Нужно создать обработчик в коде,
|     функцией handle(name, func () {  *код обработчика* }).
|
|  2. Затем можно обращаться по адресу /домен/ajax_handler/name,
|     и передавать туда нужные данные, для выполнения.
|
|  Если указанный обработчик отсутствует, выводится сообщение.
|
*/

class ajax_helper
{
    // for singleton
    private static ?ajax_helper $_instance = NULL;

    public static function getInstance() : ajax_helper
    {
        if (self::$_instance == NULL)
            self::$_instance = new ajax_helper();

        return self::$_instance;
    }

    public function __construct()
    {
        $CS = CubSystem::getInstance();
        $CS->router->all('/ajax_handler/(\w+)', function($action) use ($CS) {

            $hook_name = "cs_ajax_handle_$action";

            if(!$CS->hooks->exists($hook_name))
                echo ('invalid handler name');

            $CS->hooks->here($hook_name);

            die; // загрузку сайта останавливаем
        });

        $CS->ajax = $this;
    }

    public function handle(string $name, callable $func, callable $condition = NULL)
    {
        // можно указать свою функцию с условием
        // если не выполняется, выходим
        if ($condition != NULL && call_user_func($condition) !== TRUE)
            return;

        $CS = CubSystem::getInstance();
        $CS->hooks->register('cs_ajax_handle_' . $name, $func);
    }
}
