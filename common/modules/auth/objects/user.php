<?php  defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| user.php [rev 1.2], Назначение: класс пользователя
| -------------------------------------------------------------------------
| В этом файле описан класс пользователя
| А также, содержатся функции для работы с базой данных
| *** Для работы класса необходим хелпер mysqli_db ***
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class cs_user
{
    public $id          = NULL;
    public $name        = NULL;
    private $faction    = 0;
    private $password   = NULL;
    private $salt       = NULL;

    function __construct($data = [], $needle = FALSE)
    {
        if (is_array($data))
        {
            // all basic data
            if (isset($data['id']) && (!$needle || in_array('id', $needle)))
                $this->id = (int)$data['id'];
            if (isset($data['name']) && (!$needle || in_array('id', $needle)))
                $this->name = (string)$data['name'];
            if (isset($data['faction']) && (!$needle || in_array('id', $needle)))
                $this->faction = (int)$data['faction'];
            if (isset($data['salt']) && (!$needle || in_array('id', $needle)))
                $this->salt = (string)$data['salt'];
            if (isset($data['password']) && (!$needle || in_array('id', $needle)))
                $this->password = (string)$data['password'];
        }
    }

    public static function getById($id = FALSE, $needle = FALSE)
    {
        $id = cs_filter($id, 'int');
        return ($id) ? self::getBy($id, 'id', $needle) : NULL;
    }

    public static function getByUsername($username = FALSE, $needle = FALSE)
    {
        $username = cs_filter($username, 'username');
        return ($username) ? self::getBy($username, 'name', $needle) : NULL;
    }

    /*
    public static function getByEmail($email = FALSE)
    {
        $email = (string)$email;
        return ($email) ? self::getBy($email, 'email') : NULL;
    }*/

    private static function getBy($property = '', $selector = '', $needle = FALSE)
    {
        if (!$selector || !is_string($selector) || !$property)
            return NULL;

        global $CS;
        if (!$db = $CS->database->getInstance())
            die('[auth] Can`t connect to database');

        $db->where($selector, $property);

        if ($data = $db->getOne('users', $needle))
            return new cs_user($data);

        return NULL;
    }

    private function makePasswordHash($password)
    {
        return cs_hash_str($password . $this->salt, TRUE);
    }

    public function checkPassword($password)
    {
        return $this->makePasswordHash($password) === $this->password;
    }

    public function isAdmin()
    {
        return $this->faction === 1;
    }

    public function getFaction()
    {
        return $this->faction;
    }

    public function insert()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[auth] Can`t connect to database');

        // check user not exits in table
        if(self::getByUsername($this->name) !== NULL)
            return NULL;

        // generate random salt
        $this->salt = cs_rnd_str(16);

        $data = [
            'name'      => $this->name,
            'faction'   => $this->faction,
            'password'  => $this->makePasswordHash($this->password),
            'salt'      => $this->salt
        ];

        // function returned current user id
        $id = $db->insert('users', $data);

        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }
}
?>