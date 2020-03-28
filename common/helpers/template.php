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

    public  $joined = FALSE;
    public  $path = '';
    public  $info = [];
    public  $meta_data = [];
    private  $settings =
    [
        'minify-html' => TRUE,
    ];
    public  $body_buffer;
    public  $head_buffer;

    public function join($setpath, $fullpath = FALSE) 
    {
        if(!defined('CS__TEMPLATE_DIR'))
            define("CS__TEMPLATESPATH", CS__BASEPATH . 'templates' . _DS);

        $this->path = $fullpath ? $setpath : CS__TEMPLATESPATH . $setpath . _DS;

        // setup as joined
        $this->joined = TRUE;

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

        if($this->body_buffer == '')
            $this->body_buffer = '<div class="blank">No content</div>';

        if($print_buffer)
            print($this->body_buffer);

        global $CS;

        // minify html
        if($this->settings['minify-html'] && $minify = $CS->gc('html_minify_helper', 'helpers'))
            $this->body_buffer = $minify->minify($this->body_buffer);

        return $this->body_buffer;
    }

    public function callbackLoad($data, $callback = false)
    {
        $callback = $this->path . 'views' . _DS . $callback . '.php';
        if(!file_exists($f = $callback))
            die("[blog] Can`t load template callback file : {$callback}");

        // return as buffer output
        return cs_return_output($f, $data);
    }

    public function generateMeta($data = [])
    {
        $this->head_buffer .= "<meta name=\"generator\" content=\"CubSystem CMS\">";
        $this->head_buffer .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
        $this->head_buffer .= "<meta property=\"og:url\" content=\"{CS__BASEURL}\">";
        $this->head_buffer .= cs_autoload_css($this->path . 'assets' . _DS . 'css' . _DS);
        $this->head_buffer .= cs_autoload_js($this->path . 'assets' . _DS . 'js' . _DS);

        if(is_array($data))
            foreach ($data as $key => $value)
                switch ($key)
                {
                    case 'title':
                        $this->head_buffer .= "<title>{$value}</title>";
                        $this->head_buffer .= "<meta property='og:title' content=\"{$value}\">";
                    break;

                    case 'description':
                        $this->head_buffer .= "<meta name=\"description\" content=\"{$value}\">";
                        $this->head_buffer .= "<meta property=\"og:description\" content=\"{$value}\">";
                    break;
                }
    }
}
?>