<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

// хелпер для обработки ajax
class ajax_helper
{
    // for singleton
    private static ?ajax_helper $_instance = NULL;

    /**
     * @return ajax_helper
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new ajax_helper();

        return self::$_instance;
    }

    public function __construct()
    {
        $CS = CubSystem::getInstance();
        $CS->router->get('/ajax_handler/(\w+)', function($action) use ($CS) {
            $CS->hooks->here('cs_ajax_handle_' . $action);
        });
    }

    public function handle(string $name, callable $func)
    {
        $CS = CubSystem::getInstance();
        $CS->hooks->register('cs_ajax_handle_' . $name, $func);
    }
}