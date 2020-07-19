<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

// хелпер для обработки ajax
class ajax_helper
{
    public function __construct()
    {
        $CS = CubSystem::getInstance();
        $CS->router->get('/ajax_handler/(\w+)', function($action) use ($CS) {
            $CS->hooks->here('cs_ajax_handle_' . $action);
        });
    }
}