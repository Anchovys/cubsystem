<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class admin_helper
{
    // for singleton
    private static ?admin_helper $_instance = NULL;

    /**
     * @return admin_helper
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new admin_helper();

        return self::$_instance;
    }

    public ?admin_menu $menu = NULL;
    private string $path = CS_HELPERSPATH . 'admin' . _DS;

    public function __construct()
    {
        $CS = CubSystem::getInstance();
        $CS->admin = $this;

        require_once($this->path . 'include' . _DS . 'controller.php');
        require_once($this->path . 'include' . _DS . 'menu.php');

        $this->menu = new admin_menu();

        $this->out();
    }

    private array $cutomActions = [];
    public function setAction(string $url, callable $func)
    {
        $this->cutomActions[ucfirst($url)] = $func;
    }

    private function out()
    {
        $CS = CubSystem::getInstance();
        $CS->router->get('/admin.*', function () use ($CS)
        {
            $template = $CS->helpers->getLoaded('template');
            $template_path = $this->path . 'templates' . _DS;
            $CS->template = $template->register('admin', $template_path);

            $mainTemplate = $CS->template->getMainTmpl();

            $mainTemplate->set('addition_buttons', $this->menu->getHtml());

            $controller = new CsAdminController();
            $action = CsUrl::segment(1);
            $action = is_string($action) ? ucfirst($action) : FALSE;

            if($action != FALSE)
            {
                if(method_exists($controller, $action))
                {
                    $controller->$action();
                    return;
                }
                else if(array_key_exists($action, $this->cutomActions))
                {
                    call_user_func($this->cutomActions[$action]);
                    return;
                }
            }

            if(!$CS->hooks->here('helper_admin_404'))
            {
                $controller->Panel();
            }
        });
    }

}