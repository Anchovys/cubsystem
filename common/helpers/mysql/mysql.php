<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class mysql_helper
{
    // for singleton
    private static ?mysql_helper $_instance = NULL;

    /**
     * @return mysql_helper
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new mysql_helper();

        return self::$_instance;
    }

    private MysqliDb $object;

    public function __construct()
    {
        require_once(CS_HELPERSPATH . 'mysql/MysqliDb.php');

        $CS = CubSystem::getInstance();
        $database_config = $CS->config->getOption('database');
        if($database_config['enabled'] === TRUE)
        {
            $CS->mysql = $this;
            $this->init($database_config['connection_data']);

            try { $this->getObject()->connect(); }
            catch (Exception $e) { _pr($e); }
        }
    }

    public function init(array $connection_data)
    {
        $this->object = new MysqliDb($connection_data);
    }

    public function getObject() : MysqliDb
    {
        return $this->object;
    }
}