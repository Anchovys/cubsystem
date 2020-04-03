<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| admin.php [rev 1.0], Назначение: поддержка админ-панели сайта
| -------------------------------------------------------------------------
| В этом файле описана базовая функциональность для админ-панели
| Админ-панель имеет два хука
| cs__admin_view (для управления непосредственно админ-панелью) и
| cs__admin_handler (для обработки каких-то действий)
| Разные модули могут брать управление с этих хуков
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class admin_module extends cs_module
{
    public $options = [];

    function __construct()
    {
        global $CS;
        $this->name = "Admin";
        $this->description = "A simple adminpanel.";
        $this->version = "1";
        $this->fullpath = CS_MODULESCPATH . 'admin' . _DS;

        // connect the options
        if(file_exists($f = $this->fullpath . 'options.php'))
        {
            require_once($f);
            if(isset($options))
                $this->options = $options;
        }

        // make hook into render
        if ($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__pre-template_hook', 'view', $this);
    }

    public function view()
    {
        global $CS;
        $segments = cs_get_segment();

        // nothing to do
        if(!isset($segments[0] ) || $segments[0] !== 'admin' || !$this->checkUser())
            return;

        if(isset($segments[1]) && $segments[1] === "admin-ajax")
        {
            // ничего не обработалось из стандартных правил
            if(!$this->adminHandler($segments))
            {
                // HOOK on
                if ($h = $CS->gc('hooks_helper', 'helpers'))
                    $h->here('cs__admin-ajax');
            }
            die();
        }

        // поменяем шаблон на шаблон админки
        $tmpl_path = $this->fullpath . 'templates' . _DS . $this->options['template'] . _DS;
        $CS->template->join($tmpl_path, TRUE);

        // HOOK ON load admin
        if ($h = $CS->gc('hooks_helper', 'helpers'))
            $h->here('cs__admin-view');

        switch(isset($segments[1]) ? $segments[1] : '')
        {
            case '':
            case 'panel':
                $CS->template->callbackLoad('', 'admin/info_view', 'body');
            break;
        }
    }

    private function adminHandler($segments)
    {
        // ничего не обработано, вернем false
        return false;
    }

    // check is admin user and logged-in
    private function checkUser()
    {
        global $CS;

        if(!$auth = $CS->gc('auth_module', 'modules'))
            die('Need auth module');

        $user = $auth->getLoggedUser();

        return $user !== NULL && $user->isAdmin();
    }
}
?>