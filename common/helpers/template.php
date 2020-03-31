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
        'minify-html'           => TRUE,
        'autoload_css'          => TRUE,
        'autoload_css_path'     => FALSE,
        'autoload_js'           => TRUE,
        'autoload_js_path'      => FALSE
    ];

    private $buffers = [];


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

    public function setBuffer($name, $data, $append = FALSE)
    {
        if(array_key_exists($name, $this->buffers) && $append === TRUE)
            $this->buffers[$name] .= $data;
        else $this->buffers[$name] = $data;
    }

    public function getBuffer($name, $print = FALSE)
    {
        if(array_key_exists($name, $this->buffers))
        {
            if($print)
                print($this->buffers[$name]);

            return $this->buffers[$name];
        }
        return '';
    }

    public function render($print_buffer = TRUE)
    {
        global $CS;
        if(!file_exists($f = $this->path . 'index.php'))
            return FALSE;

        $this->setBuffer('head', "<meta name=\"generator\" content=\"CubSystem CMS\">", TRUE);
        $this->setBuffer('head', "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">", TRUE);
        $this->setBuffer('head', "<meta property=\"og:url\" content=\"{$CS->dynamic['url-address']}\">", TRUE);

        if($this->settings['autoload_css'])
        {
            $path_css = $this->settings['autoload_css_path'] ?  $this->settings['autoload_css_path'] :
                $this->path . 'assets' . _DS . 'css' . _DS . 'autoload' . _DS;
            $this->setBuffer('head', cs_autoload_css($path_css), TRUE);
        }

        if($this->settings['autoload_js'])
        {
            $path_js = $this->settings['autoload_js_path'] ?  $this->settings['autoload_js_path'] :
                $this->path . 'assets' . _DS . 'js' . _DS . 'autoload' . _DS;
            $this->setBuffer('head', cs_autoload_js($path_js), TRUE);
        }

        require_once($f);

        if(function_exists('onload_template'))
            onload_template($this);

        if($this->getBuffer('body') == '')
            $this->setBuffer('buffer', '<div class="blank">No content</div>', TRUE);

        // minify html
        if($this->settings['minify-html'] && $minify = $CS->gc('html_minify_helper', 'helpers'))
            $this->setBuffer('body', $minify->minify($this->getBuffer('body')), FALSE);

        return $this->getBuffer('body', $print_buffer);
    }

    public function callbackLoad($data, $callback = false)
    {
        $callback = $this->path . 'views' . _DS . $callback . '.php';
        if(!file_exists($f = $callback))
            die("[blog] Can`t load template callback file : {$callback}");

        // return as buffer output
        return cs_return_output($f, $data);
    }

    public function setMeta($data = [])
    {
        if(is_array($data))
            foreach ($data as $key => $value)
                switch ($key)
                {
                    case 'title':
                        $this->setBuffer('head', "<title>{$value}</title>", TRUE);
                        $this->setBuffer('head', "<meta property='og:title' content=\"{$value}\">", TRUE);
                    break;

                    case 'description':
                        $this->setBuffer('head', "<meta name=\"description\" content=\"{$value}\">", TRUE);
                        $this->setBuffer('head', "<meta property=\"og:description\" content=\"{$value}\">", TRUE);
                    break;

                    case 'stylesheet':
                        $path = $this->path . 'assets' . _DS . 'css' . _DS . 'manual' . _DS;
                        $url = cs_path_to_url($path);
                        $this->setBuffer('head', "<link rel=\"stylesheet\" href=\"{$url}{$value}\">", TRUE);
                    break;

                    case 'script':
                        $path = $this->path . 'assets' . _DS . 'js' . _DS . 'manual' . _DS;
                        $url = cs_path_to_url($path);
                        $this->setBuffer('head', "<script src=\"{$url}{$value}\"></script>", TRUE);
                    break;
                }
    }
}
?>