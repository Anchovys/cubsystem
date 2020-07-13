<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
+ -------------------------------------------------------------------------
| session.php [rev 1.0], Назначение: управление PHP сессиями
+ -------------------------------------------------------------------------
|
| Класс позволяет удобно управлять сессиями
|
*/
class CsSession
{
    // for singleton
    private static ?CsSession $_instance = NULL;

    /**
     * @return CsSession
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsSession();

        return self::$_instance;
    }

    private $prefix = 'cs_';
    private $sessionStarted = false;
    private $autoStart = TRUE;
    private $lifeTime = 0;

    public function init($options = ['lifeTime' => 0, 'autoStart' => TRUE, 'prefix' => 'cs_'])
    {
        if(!$options || !is_array($options))
            return false;

        if(isset($options['autoStart'])) $this->autoStart = (bool)$options['autoStart'];
        if(isset($options['lifeTime'])) $this->lifeTime = (int)$options['lifeTime'];
        if(isset($options['prefix'])) $this->prefix = (string)$options['prefix'];

        if($this->autoStart)
            $this->start();

        return true;
    }

    public function setPrefix($prefix)
    {
        if(is_string($prefix = trim($prefix)))
        {
            $this->prefix = $prefix;
            return TRUE;
        }
        return false;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function start()
    {
        if ($this->sessionStarted === TRUE)
            return false;

        if(empty($_SESSION))
        {
            session_set_cookie_params($this->lifeTime);
            session_start();
        }

        return $this->sessionStarted = TRUE;
    }

    /**
     * Add value to a session.
     *
     * @param mixed  $key   → name the data to save
     * @param mixed  $value → the data to save
     *
     * @return bool true
     */
    public function push($key = '', $value = FALSE)
    {
        if($key == '' || !is_scalar($key)) return FALSE;

        if (is_array($key) && $value == FALSE)
        {
            foreach ($key as $name => $value)
                $_SESSION[$this->prefix . $name] = $value;
        } else {
            $_SESSION[$this->prefix . $key] = $value;
        }

        return TRUE;
    }

    /**
     * Extract session item, delete session item and finally return the item.
     *
     * @param string $key → item to extract
     *
     * @return mixed|null → return item or null when key does not exists
     */
    public function pull($key = '')
    {
        if($key == '' || !is_scalar($key))
            return null;

        if(!array_key_exists($key, $_SESSION))
            return null;

        if(!isset($_SESSION[$this->prefix . $key]))
            return null;

        $value = $_SESSION[$this->prefix . $key];
        unset($_SESSION[$this->prefix . $key]);

        return $value;
    }

    /**
     * Get item from session.
     *
     * @param string      $key       → item to look for in session
     * @param string|bool $secondKey → if used then use as a second key
     *
     * @return mixed|null → key value, or null if key doesn't exists
     */
    public function get($key = '', $secondKey = FALSE)
    {
        if(!is_scalar($key)) return NULL;

        $name = $this->prefix . $key;

        if ($key == '')
        {
            return isset($_SESSION) ? $_SESSION : null;
        } elseif ($secondKey === TRUE)
        {
            if (isset($_SESSION[$name][$secondKey]))
                return $_SESSION[$name][$secondKey];
        }

        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Get session id.
     *
     * @return string → the session id or empty
     */
    public function id()
    {
        return session_id();
    }

    /**
     * Regenerate session_id.
     *
     * @return string → session_id
     */
    public function regen()
    {
        session_regenerate_id(TRUE);

        return session_id();
    }

    /**
     * Empties and destroys the session.
     *
     * @param string $key    → session name to destroy
     * @param bool   $prefix → if true clear all sessions for current prefix
     *
     * @return bool
     */
    public function purge($key = '', $prefix = FALSE)
    {
        if ($this->sessionStarted === FALSE)
            return FALSE;

        if ($key == '' && $prefix == FALSE)
        {
            session_unset();
            session_destroy();
        } elseif ($prefix == TRUE)
        {
            foreach ($_SESSION as $index => $value)
            {
                if (strpos($index, $this->prefix) === 0)
                    unset($_SESSION[$index]);
            }
        } else {
            unset($_SESSION[$this->prefix . $key]);
        }

        return TRUE;
    }

    public function checkSessionId(string $session_id = '')
    {
        return $session_id === $this->id();
    }
}