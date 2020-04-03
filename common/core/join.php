<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

if(file_exists($f = CS_OPTIONSPATH . 'options.php'))
    require_once($f);

if(file_exists($f = CS_COREINCPATH . 'functions.php'))
    require_once($f);

if(file_exists($f = CS_CORELIBPATH . 'cubsystem.php'))
    require_once($f);

global $CS;
if(!isset($CS) && $CS = Cubsystem::getInstance())
    $CS->init();
?>