<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */


$config = [];

$config['secret_key'] = '38kv4a';

$config['helpers'] = [
    'enabled'  => TRUE,
    'singleton_support' => TRUE,
    'priority' =>
    [
        'modules',
        'template'
    ],
    'ignore' =>
    [
    ]
];

$config['hooks'] = [
    'enabled' => TRUE
];

$config['template'] = [
    'enabled'      => TRUE,
    'default_tmpl' => 'default'
];

$config['database'] = [
    'enabled'      => TRUE,
    'connect_data' =>
    [

    ]
];

$config['cache'] = [
    'enabled' => TRUE,
    'time'    => 24 * 60 * 60
];

$config['upload'] = [
    'enabled'     => TRUE,
    'max_size_mb' => 2
];

$config['modules'] = [
    'enabled'  => TRUE,
    'autoload' =>
    [
        'landing',
        'demo',
        'welcome' // change it!
    ]
];