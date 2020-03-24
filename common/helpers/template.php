<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| template.php, Назначение: управление шаблоном сайта
| -------------------------------------------------------------------------
|
| Хелпер может управлять также, и шаблоном админ-панели
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class template_helper {

    public  $path = '';
    public  $info = [];
    public  $meta_data = [];
    private  $settings =
    [
        'minify-html' => TRUE,
    ];
    public  $html_buffer;

    public function join($setpath, $fullpath = FALSE) 
    {
        if(!defined('CS__TEMPLATE_DIR'))
            define("CS__TEMPLATESPATH", CS__BASEPATH . 'templates' . _DS);

        $this->path = $fullpath ? $setpath : CS__TEMPLATESPATH . $setpath . _DS;
 
        // get the information about template
        if(file_exists($f = $this->path . 'info.php'))
        {
            require_once($f);

            if(isset($template_info) && is_array($template_info))
                $this->info = $template_info;
        }

        // get the advanced template settings
        if(file_exists($f = $this->path . 'settings.php'))
        {
            require_once($f);

            if(isset($template_settings) && is_array($template_settings))
                $this->settings = array_merge($this->settings, $template_settings);
        }
    }

    public function render($print_buffer = TRUE)
    {
        if(!file_exists($f = $this->path . 'index.php'))
            return FALSE;

        require_once($f);

        if(function_exists('onload_template'))
            onload_template($this);
        
        if($print_buffer)
            print($this->html_buffer);

        return $this->html_buffer;
    }

    public function callback_load($data, $callback = false)
    {
        $callback = $this->path . 'views' . _DS . $callback . '.php';
        if(!file_exists($f = $callback))
            die("[blog] Can`t load template callback file : {$callback}");

        // return as buffer output
        return cs_return_output($f, $data);
    }

    public function generate_meta($data = [])
    {
        global $CS;
        $buffer = "";

        $buffer .= "<meta name=\"generator\" content=\"CubSystem CMS\">";
        $buffer .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
        $buffer .= "<meta property=\"og:url\" content=\"{CS__BASEURL}\">";
        $buffer .= cs_autoload_css($this->path . 'assets' . _DS . 'css' . _DS);
        $buffer .= cs_autoload_js($this->path . 'assets' . _DS . 'js' . _DS);

        if(is_array($data))
            foreach ($data as $key => $value)
                switch ($key)
                {
                    case 'title':
                        $buffer .= "<title>{$value}</title>";
                        $buffer .= "<meta property='og:title' content=\"{$value}\">";
                    break;

                    case 'description':
                        $buffer .= "<meta name=\"description\" content=\"{$value}\">";
                        $buffer .= "<meta property=\"og:description\" content=\"{$value}\">";
                    break;
                }

        return $buffer;
    }
}
?>