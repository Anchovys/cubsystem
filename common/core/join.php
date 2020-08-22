<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/* 
	Файл подключения основных файлов ядра.
	Не рекомендуется изменять очередность строк подключения,
	поскольку они выстроены в порядке зависимостей.
*/


/* Настройки ядра */
$cs_core = [ 
	'rev' => '1.0.1',
	'options' => []
];

/* Дополнительные функции системы */
require_once(CS_COREINCPATH . 'functions.php');

/* функции статистики */
require_once(CS_COREINCPATH . 'stats.php');

/* Системный обработчик ошибок */
require_once(CS_COREINCPATH . 'errors.php');

/* Функции для безопасности системы */
require_once(CS_COREINCPATH . 'security.php');

/* Функции для работы с сессией */
require_once(CS_COREINCPATH . 'session.php');

/* Функции для работы с файловой системой */
require_once(CS_COREINCPATH . 'filesystem.php');

/* Функции для работы с Url */
require_once(CS_COREINCPATH . 'url.php');

/* Функции для работы с информацией */
require_once(CS_COREINCPATH . 'info.php');

/* Функции для работы с конфигурацией */
require_once(CS_COREINCPATH . 'config.php');

/* Кеширование и работа с кешем */
require_once(CS_COREINCPATH . 'cache.php');

/* файлы в папке shared */
require_once(CS_COREINCPATH . 'shared.php');

/* Функции механизма куков */
require_once(CS_COREINCPATH . 'hooks.php');

/* Механизм роутов (маршруты) */
require_once(CS_COREINCPATH . 'router.php');

/* Функции для работы с хелперами */
require_once(CS_COREINCPATH . 'helpers.php');