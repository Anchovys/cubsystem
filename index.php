<?php
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

define('_DS', DIRECTORY_SEPARATOR);
define('CS__BASEPATH', dirname(realpath(__FILE__)) . _DS);

define('CS_COMMONPATH',    CS__BASEPATH   . 'common'    . _DS);
define('CS_COREPATH',      CS_COMMONPATH  . 'core'      . _DS);

require_once(CS_COREPATH . 'CubSystem.php');

global $CS;
if(!isset($CS) && $CS = CubSystem::getInstance())
{
    $CS->init(); // иницилизация системы

    /*
        тут может включаться код,
        меняющий поведение
     */

    $CS->start(); // запускаем
    $CS->out();   // выводим на эран
}