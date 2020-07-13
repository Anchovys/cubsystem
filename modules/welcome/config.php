<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
    Это персональная конфигурация
    для этого модуля.

    Конфигурация задается в массиве $config

    В массиве обязательно должны быть прописаны сл. ключи
        - min_rev   - минимальная версия движка
        - enable - включить ли модуль
*/

$config =
[
    'enable' => TRUE, // если FALSE, тогда модуль не будет загружен
    'min_rev'   => 0.08  // минимальная версия системы для работы модуля
];

// описание и название. не обязательно, но желательно!
$config['name'] = 'Welcome module Cubsystem 0.08';
$config['desc'] = 'Welcome to Cubsystem!';