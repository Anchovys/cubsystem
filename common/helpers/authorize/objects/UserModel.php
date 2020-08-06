<?php
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
    private int $_faction = 0;
    private ?string $_password = NULL;
    private ?string $_salt = NULL;

    function __construct(array $data = NULL)
    {
        if(empty($data)) return;

        // all basic data
        $id = CsSecurity::filter($data['id'], 'int');
        $name = CsSecurity::filter($data['name']);
        $email = CsSecurity::filter($data['email'], 'email');
        $faction = CsSecurity::filter($data['faction'], 'int');
        $salt = CsSecurity::filter($data['salt']);
        $password = CsSecurity::filter($data['password']);

        // check if filter fails
        if(empty($id)) return;
        if(empty($name)) return;
        if(empty($email)) return;
        if(empty($faction)) return;
        if(empty($salt)) return;
        if(empty($password)) return;

        // set
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->_faction = $faction;
        $this->_salt = $salt;
        $this->_password = $password;
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
     * @param string $property - по какому столбцу выбирать, например, id
     * @param $selector - значение столбца, например, 10.
     * @param array|null $needle - какие данные нужны от юзера
     * @return UserModel|null
     * @throws Exception
     */
    private static function _getBy(string $property, $selector, array $needle = NULL): ?UserModel
    {
        $CS = CubSystem::getInstance();

        // фильтрация входных значений
        $property = CsSecurity::filter($property, 'base|string');
        $selector = CsSecurity::filter($selector, 'base');

        // после фильтрации, переменные могут быть empty,
        // проверим это
        if(empty($property) || empty($selector))
            return NULL;

        // разрешаем доступ только к этим пропертсам
        if(!in_array($property, ['email', 'id', 'name']))
            return NULL;

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        // очистка значений
        $selector = $db->escape($selector);
        $property = $db->escape($property);

        // выборка
        $db->where($selector, $property);
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
            return NULL;

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
        try {
            $id = $db->insert('users', $data);
        } catch (Exception $e) {
            return NULL;
        }
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