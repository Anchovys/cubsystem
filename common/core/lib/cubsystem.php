<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class cs_module
{
    public $name           = null;
    public $description    = null;
    public $version        = null;
    public $fullpath       = null;
}

class Cubsystem
{
    private static $instance = null;
    public $info = [
        "version" => "0.07",
        "name"    => "Cubsystem"
    ];

    public $config       = [];
    public $dynamic     = [];
    public $hooks       = [];
    public $session     = null;
    public $database    = null;
    public $template    = null;

    public $autoload = [
        'classes' => [],
        'modules' => [],
        'helpers' => []
    ];

    function __construct()
    {
        // for statistics
        $this->dynamic['time_pre'] = microtime(TRUE);

        if(!defined('CS__BASEURL')) define('CS__BASEURL', cs_base_url());
        if(!defined('CS__FULLURL')) define('CS__FULLURL', cs_full_url());
        if(defined('CS__CFG')) $this->config = CS__CFG;
        $this->dynamic['full-url']  = CS__FULLURL;
        $this->dynamic['base-url']  = CS__BASEURL;
    }

    public static function getInstance() // for singleton
    {
      if (self::$instance == null)
        self::$instance = new Cubsystem();
   
      return self::$instance;
    }

    public function gc($classname, $category=FALSE)
    {
        $classname = strtolower(trim($classname));

        $seek_array = $category === FALSE ? FALSE : $this->autoload[$category];

        if(!$seek_array) {
            foreach(array_keys($this->autoload) as $key)
                if(array_key_exists($classname, $this->autoload[$key]))
                        return $this->autoload[$key][$classname];
            return FALSE;
            
        } else return !array_key_exists($classname, $seek_array) ? FALSE : $seek_array[$classname];
    }

    public function workingTime($suff = ' ms.')
    {
        $diff = round(microtime(TRUE) - $this->dynamic['time_pre'], 3) * 1000;
        return $diff . $suff;
    }

    public function memoryUsage()
    {
        $size = memory_get_usage(true);
        return round($size / 1024) . ' kb' ;
    }

    public function init()
    {
        /////// --> CREATING HTACCESS ////////
            
            // make htaccess if not exists
            if(!file_exists( CS__BASEPATH . '.htaccess')) 
            {
                cs_mk_htaccess();
                exit('Reload the page');
            }

        /////// CREATING HTACCESS <-- ////////

        /////// --> INSTALL CHECK ////////

            if(!$this->config || $this->config['installed'] === FALSE) 
                die("System not installed! <a href='". cs_abs_url('install') ."'>Install now!</a>");

        /////// INSTALL CHECK <-- ////////

        /////// --> HELPERS LOADING ////////

            // load all helpers
            if($custom_helpers = cs_load_helpers())
                $this->autoload['helpers'] = array_merge( $this->autoload['helpers'],  $custom_helpers );

        /////// HELPERS LOADING <-- ////////

        /////// --> MYSQL CONNECTING ////////

        // check if config skip mysql load
        if($this->config['skip_database-connect'] !== TRUE)
        {
            if(!$db = $this->gc('mysqli_db_helper', 'helpers'))
                die("Can`t load database helper! Re-install the system");

            // экземпляр остается в поле autoload[helpers] и остается
            // одним обьектом (не дублируется!)
            $this->database = mysqli_db_helper::getInstance();

            // make connection with mysql
            $this->database->addConnection('default', $this->config['database']);
            $this->database->connect('default');

            if(!$this->database)
                die('Can`t connect to MySql Database! Check your connection info');
        }

        /////// MYSQL CONNECTING <-- ////////

        /////// --> SESSION LOADING ////////

            if(!$s = $this->gc('sessions_helper', 'helpers'))
                die('Can`t load session helper! Re-install the system');

            $this->session = $s; $s = null;
            $this->session->init(['autoStart' => true]);


        /////// SESSION LOADING <-- ////////

        /////// --> PRE-MODULES HOOK ////////

        if($h = $this->gc('hooks_helper', 'helpers'))
            $h->here('cs__pre-modules_hook');

        /////// PRE-MODULES HOOK <-- ////////

        /////// --> MODULES LOADING ////////

            if($loader = $this->gc('loader_helper', 'helpers'))
            {
                // load all modules
                if($custom_modules = $loader->mod_load_for($this->config['modules']))
                    $this->autoload['modules'] = array_merge($this->autoload['modules'],  $custom_modules);
            }

        /////// MODULES LOADING <-- ////////

        /////// --> POST-MODULES HOOK ////////

        if($h = $this->gc('hooks_helper', 'helpers'))
            $h->here('cs__post-modules_hook');

        /////// POST-MODULES HOOK <-- ////////

        /////// --> TEMPLATE INIT ////////

            // trying load the template helper
            if(!$this->template = $this->gc('template_helper', 'helpers'))
                die("Can`t load template helper! Re-install the system");

            // if template not init in modules,
            // we init default template from config
            if($this->template->joined === FALSE && $this->config['template'])
                $this->template->join($this->config['template']);

        /////// TEMPLATE INIT <-- ////////

        /////// --> PRE-TEMPLATE HOOK ////////
            
            if($h = $this->gc('hooks_helper', 'helpers'))
                $h->here('cs__pre-template_hook');

        /////// PRE-TEMPLATE HOOK <-- ////////

        /////// --> TEMPLATE RENDER ////////

            // check if config skip template load
            if($this->config['skip_template'] !== TRUE)
                // print formatted page
                if(!$this->template->render())
                    die("Can`t load render template!");
                

        /////// TEMPLATE RENDER <-- ////////

        /////// --> POST-TEMPLATE HOOK ////////
            
            if($h = $this->gc('hooks_helper', 'helpers'))
                $h->here('cs__post-template_hook');

        /////// POST-TEMPLATE HOOK <-- ////////
    }
}
?>