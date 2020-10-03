<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class UserModel
{
    public ?int $id = NULL;
    public ?string $name = NULL;
    public ?string $email = NULL;
    private ?int $_faction = 0;
    private ?string $_password = NULL;
    private ?string $_salt = NULL;

    function __construct(array $data = NULL)
    {
        if(empty($data)) return;

        // достаем данные из массива по ключам
        $id = default_val_array($data, 'id', 0);
        $name = default_val_array($data, 'name');
        $email = default_val_array($data, 'email');
        $password = default_val_array($data, 'password');
        $salt = default_val_array($data, 'salt');
        $faction = default_val_array($data, 'faction', 0);

        $this->id = CsSecurity::filter($id, 'int');
        $this->name = CsSecurity::filter($name, 'username');
        $this->email = CsSecurity::filter($email, 'email');
        $this->_password = CsSecurity::filter($password, 'password');
        $this->_salt = CsSecurity::filter($salt, 'string');
        $this->_faction = CsSecurity::filter($faction, 'int');

        // проверяем обязательные поля
        if(empty_val($this->name, $this->email, $this->_password)) return;
    }

    public function isAdmin() :bool
    {
        return $this->_faction === 1;
    }

    public function getFaction() : int
    {
        return $this->_faction;
    }

    /**
     * Выбрать одну UserModel из БД по
     * property и selector
     * @param string $field - по какому столбцу выбирать, например, id
     * @param $value - значение столбца, например, 10.
     * @param array|null $needle - какие данные нужны от юзера
     * @return UserModel|null
     * @throws Exception
     */
    private static function _getBy(string $field, $value, array $needle = NULL): ?UserModel
    {
        $CS = CubSystem::getInstance();

        // фильтрация входных значений
        $field = CsSecurity::filter($field, 'base|string');
        $value = CsSecurity::filter($value, 'base');

        // после фильтрации, переменные могут быть empty,
        // проверим это
        if(empty_val($field, $value))
            return NULL;

        // разрешаем доступ только к этим полям
        if(!in_array($field, ['email', 'id', 'name']))
            return NULL;

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        // очистка значений
        $field = $db->escape($field);
        $value = $db->escape($value);

        // выборка
        $db->where($field, $value);
        if ($data = $db->getOne('users', $needle))
            return new UserModel($data);

        return NULL;
    }

    public function checkPassword(?string $password)
    {
        return $this->_makePasswordHash($password) === $this->_password;
    }

    public function checkPasswordHash(?string $passwordhash)
    {
        return $passwordhash === $this->_password;
    }

    public static function getById(int $id) : ?UserModel
    {
        $id = CsSecurity::filter($id, 'int');

        if(empty($id)) return NULL;

        try {
            return self::_getBy('id', $id);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getByUsername(string $username) : ?UserModel
    {
        $username = CsSecurity::filter($username, 'string');
        if(empty($username)) return NULL;

        try {
            return self::_getBy('name', $username);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getByEmail(string $email) : ?UserModel
    {
        $email = CsSecurity::filter($email, 'email');
        if(empty($email)) return NULL;

        try {
            return self::_getBy('email', $email);
        } catch (Exception $e) {
            return null;
        }
    }

    public function insert() : ?UserModel
    {
        $CS = CubSystem::getInstance();

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            throw new Exception("No database");

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
        try { $id = $db->insert('users', $data); }
        catch (Exception $e) { $CS->errors->handleException($e); }

        // get user from database
        return is_int($id) ? self::getById($id) : NULL;
    }

    private function _makePasswordHash(?string $password)
    {
        return CsSecurity::hash($password, $this->_salt);
    }

    private function _makePasswordSalt()
    {
        return CsSecurity::rndStr(16);
    }
}
