<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

abstract class CsModule
{
    protected array  $config;
    protected string $directory;
    protected string $classname;
    protected bool   $isLoaded = FALSE;

    public function __construct($config, $directory, $classname)
    {
        $this->config = $config;
        $this->directory = $directory;
        $this->classname = $classname;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function onLoad()
    {
        // загружен
        $this->isLoaded = TRUE;

        return TRUE;
    }

    public function onUnload()
    {
        // выгружен
        $this->isLoaded = FALSE;

        return TRUE;
    }

    public function onEnable()
    {
        // включен
        return TRUE;
    }

    public function onDisable()
    {
        // отключен
        return TRUE;
    }

    public function onPurge()
    {
        // очищены данные
        return TRUE;
    }
}