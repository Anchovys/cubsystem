<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class UserModel1 extends CsDatabaseModel
{
    public ?int $id = NULL;
    public ?string $name = NULL;
    public ?string $email = NULL;
    protected int $_faction = 0;
    protected ?string $_password = NULL;
    protected ?string $_salt = NULL;

    function __construct(array $data = NULL, array $needle = ['id', 'name', 'faction'])
    {
        parent::__construct();

        if(empty($data)) return;

        // all basic data
        if (isset($data['id']) && (!$needle || in_array('id', $needle)))
            $this->id = (int)$data['id'];

        if (isset($data['name']) && (!$needle || in_array('name', $needle)))
            $this->name = (string)$data['name'];

        if (isset($data['email']) && (!$needle || in_array('email', $needle)))
            $this->email = (string)$data['email'];

        if (isset($data['faction']) && (!$needle || in_array('faction', $needle)))
            $this->_faction = (int)$data['faction'];

        if (isset($data['salt']) && (!$needle || in_array('salt', $needle)))
            $this->_salt = (string)$data['salt'];

        if (isset($data['password']) && (!$needle || in_array('password', $needle)))
            $this->_password = (string)$data['password'];
    }

    private static function _getBy($property = '', ?string $selector = '', array $needle = NULL)
    {
        if (!$selector || !is_string($selector) || !$property)
            return NULL;

        $CS = CubSystem::getInstance();

        if ($CS->mysql === NULL) die('[auth] Can`t connect to database');
        if (!$db = $CS->mysql->getObject()) die('[auth] Can`t connect to database');

        $db->where($db->escape($selector), $db->escape($property));

        if ($data = $db->getOne('users', $needle))
            return new UserModel1($data);

        return NULL;
    }

    public static function getById(?int $id = NULL,  array $needle = NULL)
    {
        $id = CsSecurity::filter($id, 'int');

        return ($id) ? self::_getBy($id, 'id', $needle) : NULL;
    }

    public static function getByUsername(?string $username, array $needle = NULL)
    {
        $username = CsSecurity::filter($username, 'username');
        return ($username) ? self::_getBy($username, 'name', $needle) : NULL;
    }

    public static function getByEmail(?string $email)
    {
        $email = (string)$email;
        return ($email) ? self::_getBy($email, 'email') : NULL;
    }

    private function _makePasswordHash(?string $password)
    {
        return CsSecurity::hash($password, $this->_salt);
    }

    private function _makePasswordSalt()
    {
        return CsSecurity::rndStr(16);
    }

    public function checkPassword(?string $password)
    {
        return $this->_makePasswordHash($password) === $this->_password;
    }

    public function isAdmin()
    {
        return $this->_faction === 1;
    }

    public function getFaction()
    {
        return $this->_faction;
    }

    public function insert()
    {
        $CS = CubSystem::getInstance();

        if ($CS->mysql === NULL) die('[auth] Can`t connect to database');
        if (!$db = $CS->mysql->getObject()) die('[auth] Can`t connect to database');

        // generate random salt
        $this->_salt = $this->_makePasswordSalt();

        $data =
            [
                'name'      => $db->escape($this->name),
                'email'     => $db->escape($this->email),
                'faction'   => $db->escape($this->_faction),
                'password'  => $this->_makePasswordHash($this->_password),
                'salt'      => $this->_salt
            ];

        // function returned current user id
        $id = $db->insert('users', $data);

        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }
}