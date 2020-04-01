<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| options.php [rev 1.1], Назначение: управление конфигурацией Cubsystem
| -------------------------------------------------------------------------
| В этом файле описаны базовые настройки, используемые системой
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

define("CS__CFG",
[
    // database connection info
    'database'  => [
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'db'       => 'cubsystem',
        'port'     => 3306,
        'prefix'   => 'cs_',
        'charset'  => 'utf8'
    ],

    // skip database connection
    // WARNING: if you enable that option
    // ALL HELPERS/MODULES/PLUGINS using DB will not work
    'skip_database-connect' => FALSE,

    // template-name in catalog ROOT/templates
    'template'  => 'example',

    // skip template loading
    // if enable, template stop rendering
    'skip_template' => FALSE,

    // system is installed?
    'installed' => TRUE,

    // secret key
    // type your secret key here
    'secret_key' => 'default_key',

    // lot of helpers for load firstly
    'helpers-priority' =>
    [
        'debugging',    //first
        'filters',      //second
        'sessions',     // ...
        'mysqli_db',
        'hooks',
        'loader'
    ],

    // array of modules for load on system load
    'modules'   =>
    [
        'auth',
        'adminpanel',
        'blog'
    ],

    // enable hook system
    'enable_hooks' => TRUE
]);

?>