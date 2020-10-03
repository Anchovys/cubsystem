<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
$config =
    [
        'enable' => TRUE,
        'min_rev'   => 0.08
    ];

$config['name'] = 'Генератор ачивок Minecraft';
$config['desc'] = 'Генерирует ачивки';
$config['version'] = '0.1';

$config['route']['root'] = "achievements/generator/";
$config['route']['generate'] = "generate_new";
$config['route']['get_icons'] = "get_icons";

// настройки телеграм бота
$config['telegram_bot']['use'] = TRUE;
$config['telegram_bot']['token'] = "1334660093:AAEBHXApjpwWwIaQ1M1WLmdIYC9zEbBIx0s";
$config['telegram_bot']['ips'] = [];
$config['route']['telegram_bot'] = "telegram_bot";
