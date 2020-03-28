<?php  defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| user.php, Назначение: класс пользователя
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
    public  $id          = NULL;
    public  $name        = NULL;
    public  $faction     = 0;
    private $password    = NULL;
    private $salt        = NULL;

    function __construct($data = [])
    {
        if(is_array($data))
        {
            // all basic data
            if (isset($data['id']))         $this->id = (int)$data['id'];
            if (isset($data['name']))       $this->name = (string)$data['name'];
            if (isset($data['faction']))    $this->faction = (int)$data['faction'];
            if (isset($data['salt']))       $this->salt = (string)$data['salt'];
            if (isset($data['password']))   $this->password = (string)$data['password'];
        }
    }

    public static function getById($id = FALSE)
    {
        $id = (int)$id;
        return ($id) ? self::getBy($id, 'id') : NULL;
    }

    public static function getByNickname($nickname = FALSE)
    {
        $nickname = (string)$nickname;
        return ($nickname) ? self::getBy($nickname, 'name') : NULL;
    }

    /*
    public static function getByEmail($email = FALSE)
    {
        $email = (string)$email;
        return ($email) ? self::getBy($email, 'email') : NULL;
    }*/

    private static function getBy($property = '', $selector = '')
    {
        if(!$selector || !is_string($selector) || !$property)
            return NULL;

        global $CS;
        if(!$db = $CS->database->getInstance())
            die('[auth] Can`t connect to database');

        $db->where($selector, $property);

        if($data = $db->getOne('users'))
            return new cs_user($data);

        return NULL;
    }

    public function checkPassword($password)
    {
        return cs_hash_str($password . $this->salt, TRUE) === $this->password;
    }

    public function insert()
    {
        global $CS;

        if(!$db = $CS->database->getInstance())
            die('[auth] Can`t connect to database');

        // check user not exits in table
        if(self::getByNickname($this->name) !== NULL)
            return NULL;

        // generate random salt
        $salt = cs_get_random_str(16);

        $data = [
            'name'      => $this->name,
            'faction'   => $this->faction,
            'password'  => cs_hash_str($this->password .  $salt, TRUE),
            'salt'      => $salt
        ];

        // function returned current user id
        $id = $db->insert('users', $data);

        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }

}
?>