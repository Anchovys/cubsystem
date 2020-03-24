<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| options.php [rev 1.0], Назначение: управление конфигурацией Cubsystem
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
    // system is installed?
    'installed' => TRUE,

    // skip template loading
    'skip_template' => FALSE,

    // skip database connection
    'skip_database-connect' => FALSE,

    // array of modules for load on system load
    'modules'   => ['blog'],

    // template-name in catalog ROOT/templates
    'template'  => 'example',

    // database connection info
    'database'  => [
        'host'     => 'localhost',
        'username' => 'root', 
        'password' => '',
        'db'       => 'cubsystem',
        'port'     => 3306,
        'prefix'   => 'cs_',
        'charset'  => 'utf8'
    ]
]);

?>