<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

$config = [];

$config['security'] = [
    'secret_key' => 'secret_key'
];

$config['helpers'] = [
    'enabled'  => TRUE,
    'singleton_support' => TRUE,
    'priority' =>
    [
        'ajax',
        'template',
        'mysql',
        'authorize',
        'admin',
        'modules'
    ],
    'ignore' =>
    [
    ]
];

$config['hooks'] = [
    'enabled' => TRUE
];

$config['ajax'] = [
    'enabled' => TRUE
];

$config['admin'] = [
    'enabled' => TRUE,
    'helper'  => 'admin'
];

$config['template'] = [
    'enabled'      => TRUE,
    'default_tmpl' => 'default'
];

$config['database'] = [
    'enabled'         => FALSE, // mysql are disabled
    'helper'          => 'mysql',
    'error_ignore'    => FALSE,
    'connection_data' =>
    [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'db'=> 'cubsystem',
        'port' => 3306,
        'prefix' => 'cs_',
        'charset' => 'utf8'
    ]
];

$config['auth'] = [
    'enabled'         => FALSE, // mysql are disabled
    'helper'          => 'authorize'
];

$config['upload'] = [
    'enabled'     => TRUE,
    'max_size_mb' => 2
];

$config['modules'] = [
    'enabled'  => TRUE,
    'helper'   => 'modules',
    'autoload' =>
    [
        'landing',
        'demo',
        'welcome' // change it!
    ]
];