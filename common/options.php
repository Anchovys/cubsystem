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
    'installed' => TRUE,
    'modules'   => ['blog'],
    'template'  => 'example',
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