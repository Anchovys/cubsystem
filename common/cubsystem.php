<?php
class Cubsystem 
{
    public $info = [
        "version" => "0.01",
        "name"    => "Cubsystem"
    ];

    public $config  = [];
    public $dynamic = [];
    public $classes = [];
    public $hooks   = [];

    public $autoload = [
        'classes' => [],
        'modules' => []
    ];

    function __construct()
    {
        // for statistics
        $this->dynamic['time_pre'] = microtime(true);

        // http-address
        $base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
        if(!defined('CS__BASEURL')) define('CS__BASEURL', $base_url);

        // get url segments
        $segments = cs_get_segments();
        if(!defined('CS__SEGMENTS')) define('CS__SEGMENTS', $segments);

        // для унификации
        if(defined('CS__CFG')) $this->config = CS__CFG;
        $this->dynamic['url-address']  = CS__BASEURL;
        $this->dynamic['url-segments'] = $segments;
    }

    public function gc ($classname)
    {
        $classname = trim($classname);
        $classname = strtolower($classname);
        
        return array_key_exists($classname, $this->classes) ? $this->classes[$classname] : false;
    }

    public function init()
    {
        $this->dynamic['page_data'] =
        [
            'body' => '<h1>Content not found — 404</h1>', 
            'head' => cs_autoload_css() . cs_autoload_js()
        ];

        // load all helpers
        $custom_helpers = cs_load_helpers();
        $this->classes = array_merge( $this->classes,  $custom_helpers );

        // make htaccess if not exists
        if(!file_exists( CS__BASEPATH . '.htaccess')) 
        {
            cs_make_htaccess(); 
            exit('Reload the page');
        }

        // handle the routes
        cs_handle_routes();

        // print formatted page
        print(cs_template_output($this->dynamic['page_data']));
    }
}
?>