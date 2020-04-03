<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

require_once(CS_OPTIONSPATH . 'options.php');

require_once(CS_COREINCPATH . 'debugging.php');
require_once(CS_COREINCPATH . 'strings.php');
require_once(CS_COREINCPATH . 'filter.php');
require_once(CS_COREINCPATH . 'url.php');
require_once(CS_COREINCPATH . 'fs.php');
require_once(CS_COREINCPATH . 'helpers.php');
require_once(CS_COREINCPATH . 'template.php');

require_once(CS_CORELIBPATH . 'cubsystem.php');

global $CS;
if(!isset($CS) && $CS = Cubsystem::getInstance())
    $CS->init();
?>