<?php
define('_DS', DIRECTORY_SEPARATOR);
define('CS__BASEPATH', dirname(realpath(__FILE__)) . _DS);

define('CS_COMMONPATH',    CS__BASEPATH  . 'common'  . _DS);
define('CS_HELPERSPATH',   CS_COMMONPATH . 'helpers' . _DS);
define('CS_MODULESCPATH',  CS_COMMONPATH . 'modules' . _DS);
define('CS_OPTIONSPATH',   CS_COMMONPATH . 'options' . _DS);

define('CS_COREPATH',      CS_COMMONPATH . 'core'    . _DS);
define('CS_COREINCPATH',   CS_COREPATH   . 'inc'     . _DS);
define('CS_CORELIBPATH',   CS_COREPATH   . 'lib'     . _DS);

// ядро системы
if(file_exists($f = CS_COREPATH . 'join.php'))
    require_once($f);