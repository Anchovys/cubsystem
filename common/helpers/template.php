<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| template.php [rev 1.1], Назначение: управление шаблоном сайта
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

class template_helper
{
    public  $joined     = FALSE;
    public  $path       = '';
    public  $url        = '';
    public  $info       = [];
    public  $meta_data  = [];
    private $options    = [];
    private $buffers    = [];
    private $pagination = NULL;

    public function getOption($key = '')
    {
        return array_key_exists($key, $this->options)
            ? $this->options[$key] : FALSE;
    }

    /**
     * Простой шаблонизатор
     * Можно использовать сторонний хелпер, но пока так
     * @param string $code - код который нужно выполнить
     * @return string
     */
    private function tmplPrepare($code = '')
    {
        $code = '?>' . str_replace(['{{', '}}', '{%', '%}'], ['<?=', '?>', '<?php', '?>'], $code);
        return $code;
    }

    public function join($setpath, $fullpath = FALSE)
    {
        if (!defined('CS__TEMPLATESPATH'))
            define("CS__TEMPLATESPATH", CS__BASEPATH . 'templates' . _DS);

        $this->path = $fullpath ? $setpath : CS__TEMPLATESPATH . $setpath . _DS;
        $this->url  = cs_path_to_url($this->path);

        // setup as joined
        $this->joined = TRUE;

        // get the information about template
        if (file_exists($f = $this->path . 'info.php')) {
            require_once($f);

            if (isset($template_info) && is_array($template_info))
                $this->info = $template_info;
        }

        // default options
        $this->options = [
            'minify-html' => TRUE,
            'autoload_css' => TRUE,
            'autoload_css_path' => FALSE,
            'autoload_js' => TRUE,
            'autoload_js_path' => FALSE,
            'custom_meta'   =>
            [
                'favicon' => 'assets/img/favicon.png',
            ],
            'main_view' => 'main_view',
            'tmpl_prepare' => TRUE,
            'pagination' => [
                'enable' => true,
                'limit'  => 10,
                'index'  => 'next'
            ],
        ];

        // get the advanced template settings
        if (file_exists($f = $this->path . 'options.php')) {
            require_once($f);
        }

        if($this->options['pagination']['enable'] === TRUE)
        {
            include_once (CS_HELPERSPATH . 'template' . _DS . 'pagination.php');

            $this->pagination = new cs_pagination();

            if(isset($this->options['pagination']['max_links']))
                $this->pagination->setMaxLinks((int)$this->options['pagination']['max_links']);
            if(isset($this->options['pagination']['index']))
                $this->pagination->setIndex($this->options['pagination']['index']);
            if(isset($this->options['pagination']['limit']))
                $this->pagination->setLimit((int)$this->options['pagination']['limit']);
        }
    }

    public function setBuffer($name, $data, $append = FALSE)
    {
        if (array_key_exists($name, $this->buffers) && $append === TRUE)
            $this->buffers[$name] .= $data;
        else $this->buffers[$name] = $data;
    }

    public function getPagination()
    {
        return $this->pagination ? $this->pagination : FALSE;
    }
    public function showBuffer($name, $print = FALSE, $purge = FALSE)
    {
        if (array_key_exists($name, $this->buffers)) {

            $b = $this->buffers[$name];

            if($purge)
                $this->buffers[$name] = '';

            if ($print)
                print($b);

            return $b;
        }
        return '';
    }
    public function render($print_buffer = TRUE)
    {
        global $CS;
        if(!file_exists($f = $this->path . 'index.php'))
            return FALSE;

        $this->setBuffer('head', "<meta charset=\"UTF-8\">", TRUE);
        $this->setBuffer('head', "<meta name=\"generator\" content=\"CubSystem CMS\">", TRUE);
        $this->setBuffer('head', "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">", TRUE);
        $this->setBuffer('head', "<meta property=\"og:url\" content=\"{$CS->dynamic['full-url']}\">", TRUE);

        if(is_array($this->options['custom_meta']))
            $this->setMeta($this->options['custom_meta']);

        if($this->options['autoload_css'])
        {
            $path_css = $this->options['autoload_css_path'] ?  $this->options['autoload_css_path'] :
                $this->path . 'assets' . _DS . 'css' . _DS . 'autoload' . _DS;
            $this->setBuffer('head', csAutoloadCss($path_css), TRUE);
        }

        if($this->options['autoload_js'])
        {
            $path_js = $this->options['autoload_js_path'] ?  $this->options['autoload_js_path'] :
                $this->path . 'assets' . _DS . 'js' . _DS . 'autoload' . _DS;
            $this->setBuffer('head', csAutoloadJs($path_js), TRUE);
        }

        // no content view
        if(strlen($this->showBuffer('body')) == 0)
            $this->callbackLoad([], 'no-content', 'body');

        require_once($f);

        if(function_exists('onload_template'))
            onload_template($this);


        // minify html
        if($this->options['minify-html'] && $minify = $CS->gc('html_minify_helper', 'helpers'))
            $this->setBuffer('body', $minify->minify($this->showBuffer('body')), FALSE);

        return $this->showBuffer('body', $print_buffer, TRUE);
    }

    public function callbackLoad($data, $callback = FALSE, $appendBuffer = FALSE)
    {
        $callback = $this->path . 'views' . _DS . $callback . '.php';
        if(!file_exists($f = $callback))
            die("[blog] Can`t load template callback file : {$callback}");


        $buffer = !$this->options['tmpl_prepare'] ? csReturnOutput($f, $data) :
            csReturnOutput($f, $data, function ($args)
            { /* Вызов шаблонизатора здесь */
                return $this->tmplPrepare($args);
            });

        if($appendBuffer !== FALSE && is_string($appendBuffer))
            $this->setBuffer($appendBuffer, $buffer, TRUE);

        // return as buffer output
        return $buffer;
    }

    public function setMeta($data = [])
    {
        if(is_array($data))
            foreach ($data as $key => $value)
                switch ($key)
                {
                    case 'title':
                        $this->setBuffer('head', "<title>{$value}</title>", TRUE);
                        $this->setBuffer('head', "<meta property=\"og:title\" content=\"{$value}\">", TRUE);
                    break;

                    case 'description':
                        $this->setBuffer('head', "<meta name=\"description\" content=\"{$value}\">", TRUE);
                        $this->setBuffer('head', "<meta property=\"og:description\" content=\"{$value}\">", TRUE);
                    break;

                    case 'icon':
                    case 'favicon':
                        $url = $this->url . 'assets/img/favicons/';
                        $this->setBuffer('head', "<link rel=\"icon shortcut\" href=\"{$url}{$value}\" type=\"image/x-icon\">", TRUE);
                        break;

                    case 'css':
                    case 'stylesheet':
                        $url = $this->url . 'assets/css/manual/';
                        $this->setBuffer('head', "<link rel=\"stylesheet\" href=\"{$url}{$value}\">", TRUE);
                    break;

                    case 'js':
                    case 'script':
                        $path = $this->path . 'assets' . _DS . 'js' . _DS . 'manual' . _DS;
                        $url = cs_path_to_url($path);
                        $this->setBuffer('head', "<script src=\"{$url}{$value}\"></script>", TRUE);
                    break;
                }
    }
}