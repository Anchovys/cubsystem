<?php
define('_DS', DIRECTORY_SEPARATOR);
define('CS__BASEPATH', dirname(realpath(__FILE__)) . _DS);

define('CS_CUBSYSTEMPATH', CS__BASEPATH      . 'cubsystem' . _DS);
define('CS_COMMONPATH',    CS_CUBSYSTEMPATH  . 'common'    . _DS);
define('CS_COREPATH',      CS_COMMONPATH     . 'core'      . _DS);

// ядро системы
if(file_exists($f = CS_COREPATH . 'join.php'))
    require_once($f);

// вызов запуска
if(function_exists("cs_start"))
    cs_start();