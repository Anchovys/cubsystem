<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

define('CS_MODULESCPATH', CS__BASEPATH     . 'modules' . _DS);

define('CS_CONFIGPATH',   CS_CUBSYSTEMPATH . 'config'  . _DS);
define('CS_HELPERSPATH',  CS_COMMONPATH    . 'helpers' . _DS);
define('CS_TEMPPATH',     CS_COMMONPATH    . 'temp'    . _DS);

define('CS_COREINCPATH',  CS_COREPATH      . 'inc'     . _DS);
define('CS_CORELIBPATH',  CS_COREPATH      . 'lib'     . _DS);


require_once(CS_CONFIGPATH . 'config.php');

require_once(CS_COREINCPATH . 'debugging.php');
require_once(CS_COREINCPATH . 'security.php');
require_once(CS_COREINCPATH . 'filter.php');
require_once(CS_COREINCPATH . 'url.php');
require_once(CS_COREINCPATH . 'fs.php');
require_once(CS_COREINCPATH . 'helpers.php');
require_once(CS_COREINCPATH . 'template.php');

require_once(CS_CORELIBPATH . 'cubsystem.php');

function cs_start()
{
    global $CS;
    if(!isset($CS) && $CS = Cubsystem::getInstance())
        $CS->init();
}