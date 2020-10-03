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

    private array $customActions = [];
    public function setAction(string $url, callable $func)
    {
        $this->customActions[ucfirst($url)] = $func;
    }

    public function handleAjax(string $name, callable $func)
    {
        $CS = CubSystem::getInstance();
        $CS->ajax->handle($name, $func, function () {
            return $this->hasAccess();
        });
    }

    public function hasAccess() : bool
    {
        $CS = CubSystem::getInstance();
        $user = $CS->auth->getCurrent();
        if($user === NULL)
           return FALSE;

        return $user->isAdmin();
    }

    private function out()
    {
        $CS = CubSystem::getInstance();
        $CS->router->all('/admin.*', function () use ($CS)
        {
            // директория шаблонов
            $template_path = $this->path . 'templates' . _DS;

            // получаем хелпер шаблона
            $template = $CS->helpers->getLoaded('template');

            // регистранция админ шаблона
            $CS->template = $template->register('admin', $template_path);

            $mainTemplate = $CS->template->getMainTmpl();

            // нет доступа
            if($this->hasAccess() !== TRUE)
            {
              $loginTemplate = new CsTmpl('auth/login', $CS->template);
              $loginTemplate->set('token', $CS->info->getOption('security_CSRF-secure_token'));
              $mainTemplate->set('content', $loginTemplate->out());
              return;
            }

            // меню
            $menuTemplate = new CsTmpl('admin/menu', $CS->template);
            $mainTemplate->set('menu', $menuTemplate->set('addition_buttons', $this->menu->getHtml())->out());


            // панель юзера
            $authbarTemplate = new CsTmpl('admin/authbar', $CS->template);
            $mainTemplate->set('authbar', $authbarTemplate->out());

            $action = CsUrl::segment(1);
            $action = is_string($action) ? ucfirst($action) : FALSE;

            $controller = new CsAdminController();

            if($action != FALSE)
            {
                if(method_exists($controller, $action))
                {
                    $controller->$action();
                    return;
                }
                else if(array_key_exists($action, $this->customActions))
                {
                    call_user_func($this->customActions[$action]);
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
