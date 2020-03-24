<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| template.php, Назначение: управление шаблоном сайта
| -------------------------------------------------------------------------
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class template_helper {

    private $path = '';
    public  $info = [];

    public function join($name) 
    {
        define("CS__TEMPLATESPATH",             CS__BASEPATH        . 'templates'    . _DS);
        define('CS__TEMPLATE_DIR',              CS__TEMPLATESPATH   . $name          . _DS);
        define('CS__TEMPLATE_ASSETS_DIR',       CS__TEMPLATE_DIR    . 'assets'       . _DS);
        define('CS__TEMPLATE_VIEWS_DIR',        CS__TEMPLATE_DIR    . 'views'        . _DS);

        $this->path = CS__TEMPLATE_DIR;

        // get the information about template
        if(file_exists($f = $this->path . 'info.php'))
        {
            require_once($f);
            $this->info = $template_info;
        }
    }

    public function render()
    {
        if(!file_exists($f = $this->path . 'index.php'))
            return false;

        require_once($f);

        if(function_exists('onload_template'))
            onload_template();
        
        return true;
    }
}
?>