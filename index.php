<?php
define('_DS', DIRECTORY_SEPARATOR);
define('CS__BASEPATH', dirname(realpath(__FILE__)) . _DS);
define('CS__KERNELPATH', CS__BASEPATH   . 'common' . _DS);

if(file_exists($f = CS__KERNELPATH . 'options.php'))
    require_once($f);

if(file_exists($f = CS__KERNELPATH . 'kernel.php'))
    require_once($f);

if(file_exists($f = CS__KERNELPATH . 'cubsystem.php'))
    require_once($f);

if(!isset($CS) && $CS = new Cubsystem())
    $CS->init();
?>