<?php
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

define('_DS', DIRECTORY_SEPARATOR);
define('CS__BASEPATH', dirname(realpath(__FILE__)) . _DS);

define('CS_COMMONPATH',    CS__BASEPATH   . 'common'    . _DS);

require_once(CS_COMMONPATH . 'CubSystem.php');

global $CS;
if(!isset($CS) && $CS = CubSystem::getInstance())
{
    $CS->init(); // иницилизация системы

    /*
        тут может включаться код,
        меняющий поведение
     */

    $CS->start(); // запускаем
    $CS->out();   // выводим на экран
}