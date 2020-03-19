<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
class Cubsystem 
{
    public $info = [
        "version" => "0.02",
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

    function __construct ()
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
        $classname = strtolower(trim($classname));
        return !array_key_exists($classname, $this->classes) ? false : $this->classes[$classname];
    }

    public function working_time ()
    {
        $diff = microtime(true) - $this->dynamic['time_pre'];
        return $diff . 'ms.';
    }

    public function init ()
    {
        /////// --> HELPERS LOADING ////////

            // load all helpers
            $custom_helpers = cs_load_helpers();
            $this->classes = array_merge( $this->classes,  $custom_helpers );

        /////// HELPERS LOADING <-- ////////

        /////// --> CREATING HTACCESS ////////
            
            // make htaccess if not exists
            if(!file_exists( CS__BASEPATH . '.htaccess')) 
            {
                cs_make_htaccess(); 
                exit('Reload the page');
            }

        /////// CREATING HTACCESS <-- ////////

        /////// --> INSTALL CHECKED ////////

            if(!$this->config || $this->config['installed'] === FALSE) 
                die("System not installated! <a href='{$base_url}install'>Install now!</a>");

        /////// INSTALL CHECKED <-- ////////

        /////// --> MYSQL CONNECTION ////////

            if(!$db = $this->gc('mysqli_db_helper'))
                die("Can`t load database helper!");

            // make connection with mysql
            $db->addConnection('default', $this->config['database']);
            $db->connect('default');

            if(!$db) die('Can`t connect to MySql Database');

        /////// MYSQL CONNECTION <-- ////////

        // handle the routes
        # cs_handle_routes();

        // print formatted page
        cs_execute_template();
    }
}
?>