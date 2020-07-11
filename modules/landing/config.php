<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */


$config =
[
    'enable' => TRUE, // если FALSE, тогда модуль не будет загружен
    'min_rev'   => 0.10  // минимальная версия системы для работы модуля
];

// описание и название. не обязательно, но желательно!
$config['name'] = 'Модуль лендинга для CubSystem 0.10';
$config['desc'] = 'Подключает все страницы из /pages/';