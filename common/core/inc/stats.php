<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class CsStats
{
    public static function getUsingMemoryString()
    {
        $mem_usage = memory_get_usage(); // Currently used memory
        return 'memory: ' . round($mem_usage / 1024) . 'kb';
    }

    public static function getUsingPeakString()
    {
        $mem_peak = memory_get_peak_usage(); // Peak memory usage
        return 'peak: ' . round($mem_peak / 1024) . 'kb.';
    }
    public static function getTimeInSeconds()
    {
        $CS = CubSystem::getInstance();
        $time = microtime(TRUE) - $CS->info->getOption('start_time');
        return round($time, 3);
    }
}