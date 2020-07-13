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
    'enable' => TRUE,    // если FALSE, тогда модуль не будет загружен
    'min_rev'   => 0.10  // минимальная версия системы для работы модуля
];

/* 
    Кастомный путь к директории, в которой определены страницы.
    Здесь вы можете указать свой, относительно BASEPATH.
    Если хотите по-умолчанию, тогда ничего не указывайте.
*/
$config['module']['pages_path'] = '';

/* 
    Главная страница (пустой route).
    Здесь вы можете указать, какая страница должна
    сработать (в каталоге /pages/), если указать
    главную страницу сайта, то есть site.ru/.

    Если не хотите определять главную страницу, 
    тогда ничего не указывайте.
*/
$config['module']['main_page'] = 'home';

/* 
    Страница в случае неудачи роутинга.
    Здесь вы можете указать, какая страница должна
    сработать (в каталоге /pages/), если ни один
    из маршрутов не подошел.

    Если не хотите определять страницу 404, 
    тогда ничего не указывайте.
*/
//$config['module']['404_page'] = 'home';

/* 
    Главный файл в директории страницы.
    Основной файл, на который нужно ссылаться,
    например, /home/index.php, где index.php- файл,
    который будет открыт при входе в site.com/home/.
*/
$config['module']['index_file'] = 'index.php';

/*
    Регистрировать файлы для страниц.
    Использовать ли файлы для определения страниц,
    а не только директории, например /home/blog.php,
    отработает при входе site.com/home/blog.

    $config['module']['file_as_page'] = TRUE;
*/

/* 
    Глубина сканирования.
    Указывает глубину сканирования каталогов,
    в каталоге /pages/, для задания страниц.
    Например: /pages/home/blog/comments 
    - глубина = 3
*/
$config['module']['scan_depth'] = 5;

/* Настройка модуля */
$config['name'] = 'Модуль лендинга для CubSystem 0.10';
$config['desc'] = 'Подключает все страницы из /pages/';