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
        $this->name = "Admin";
        $this->description = "A simple adminpanel.";
        $this->version = "1";
        $this->fullpath = CS_MODULESCPATH . 'admin' . _DS;
        $this->config['autoload'] = TRUE;
    }

    // on load module
    public function onLoad()
    {
        global $CS;

        // connect the options
        if (file_exists($f = $this->fullpath . 'options.php')) {
            require_once($f);
            if (isset($options))
                $this->options = $options;
        }

        // make hook into render
        if ($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__pre-template_hook', 'view', $this);

        // set is loaded true
        $this->isLoaded = true;
    }

    // on unload module
    public function onUnload()
    {

    }

    // on system install
    public function onInstall()
    {

    }

    // on enable that module
    public function onEnable()
    {

    }

    // on disable that module
    public function onDisable()
    {

    }

    public function view()
    {
        global $CS;
        $segments = cs_get_segment();

        // nothing to do
        if(!isset($segments[0] ) || $segments[0] !== 'admin' || !$this->_checkUser())
            return;

        if(isset($segments[1]) && $segments[1] === "admin-ajax")
        {
            // ничего не обработалось из стандартных правил
            if(!$this->_admin_ajax($segments))
            {
                // HOOK on
                if ($h = $CS->gc('hooks_helper', 'helpers'))
                    $h->here('cs__admin-ajax');
            }


            return;
        }

        // поменяем шаблон на шаблон админки
        $tmpl_path = $this->fullpath . 'templates' . _DS . $this->options['template'] . _DS;
        $CS->template->join($tmpl_path, TRUE);

        // ничего не обработалось из стандартных правил
        if(!$this->_admin_view($segments))
        {
            // HOOK ON load admin
            if ($h = $CS->gc('hooks_helper', 'helpers'))
                $h->here('cs__admin-view');
        }
    }

    private function _admin_ajax($segments)
    {
        if($segments[2] === 'upload-file')
        {
            define("UPLOADPATH", CS__BASEPATH . 'uploads');

            $file = $_FILES['file'];

            if(!isset($_FILES) || !array_key_exists('file', $_FILES))
                return;

            $file_name = $file['name'];
            $file_type = $file['type'];
            $file_size = $file['size'];
            $tmp_name  = $file['tmp_name'];
            $tar_name  = basename($file['name']);

            // allow image types
            $allow = [
                'jpg'   => 'image/jpg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'png'   => 'image/png'
            ];

            // check file type
            if(!array_key_exists(cs_file_ext($file_name), $allow) || !in_array($file_type, $allow))
                die("Incorrect format, allow: jpg/jpeg; gif; png only!");

            // 1 mb max
            if($file_size > 1 * 1024 * 1024)
                die("1MB filesize - max");

            if(!file_exists(CS_UPLOADSPATH) || !is_dir(CS_UPLOADSPATH))
            {
                mkdir(CS_UPLOADSPATH, 0777) or die('Can`t make /uploads/ path');
                //chmod('CS_UPLOADSPATH', 0777);
            }

            if(file_exists(CS_UPLOADSPATH . $tar_name))
                die('Already exists');

            $res = move_uploaded_file($tmp_name, CS_UPLOADSPATH . $tar_name);

            die($res ? 'Ok':'Fail');

            return TRUE;
        }

        return FALSE;
    }

    private function  _admin_view($segments)
    {
        global $CS;
        $segments[1] = (isset($segments[1]) ? $segments[1] : '' );

        if($segments[1] === '' or $segments[1] === 'panel')
        {
            $CS->template->callbackLoad('', 'admin/info_view', 'body');
            return TRUE;
        }

        return FALSE;
    }


    // check is admin user and logged-in
    private function _checkUser()
    {
        global $CS;

        if(!$auth = $CS->gc('auth_module', 'modules'))
            die('Need auth module');

        $user = $auth->getLoggedUser();

        return $user !== NULL && $user->isAdmin();
    }
}