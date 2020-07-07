<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

$config = [];

$config['secret_key'] = '38kv4a';

$config['helpers'] = [
    'enabled'  => TRUE,
    'singleton_support' => TRUE,
    'priority' =>
    [
        'database',
        'modules',
        'template',
        'upload'
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
        'demo',
        'welcome' // change it!
    ]
];