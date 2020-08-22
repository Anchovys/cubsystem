<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class authorize_helper
{
    // for singleton
    private static ?authorize_helper $_instance = NULL;

    /**
     * @return authorize_helper
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new authorize_helper();

        return self::$_instance;
    }

    private ?UserModel $_currentUser;

    public function __construct()
    {
        $CS = CubSystem::getInstance();
        $CS->auth = $this;

        require_once(CS_HELPERSPATH . 'authorize/objects/UserModel.php');

        $this->_currentUser = $this->currentUserDetect();

        // инициализируем ajax
        if ($CS->helpers->getLoaded('ajax') !== NULL)
        {
            $CS->ajax->handle('login', function () {

                /* Задержка 3 секунды - простая защита от перебора */
                sleep(3);

                $username = default_val_array($_POST, 'username');
                $username = CsSecurity::filter($username, 'username');

                $password = default_val_array($_POST, 'password');
                $password = CsSecurity::filter($password, 'password');

                if(empty_val($username, $password))
                    return FALSE;

                $login = $this->logIn($username, $password);

                CsUrl::redir('');

                die($login ? 'ok' : 'fail');
            });

            $CS->ajax->handle('register', function () {

                /* Задержка 3 секунды - простая защита от перебора */
                sleep(3);

                $username = default_val_array($_POST, 'username');
                $username = CsSecurity::filter($username, 'username');

                $password = default_val_array($_POST, 'password');
                $password = CsSecurity::filter($password, 'password');

                $email = default_val_array($_POST, 'email');
                $email = CsSecurity::filter($email, 'email');

                if(empty_val($username, $password, $email))
                    return FALSE;

                $register = $this->register($username, $password, $email);

                CsUrl::redir('');
                die($register ? 'ok' : 'fail');
            });

            $CS->ajax->handle('logout', function () {

                $logout = $this->logOut();

                CsUrl::redir('');
                die($logout ? 'ok' : 'fail');
            });
        }
    }

    public function getCurrent() : ?UserModel
    {
        return $this->_currentUser;
    }

    public function logIn(?string $username, ?string $password)
    {
        if($this->currentUserDetect() != NULL) return FALSE;

        $user = UserModel::getByUsername($username);

        if($user === NULL || !$user->checkPassword($password))
            return FALSE;

        $this->makeUserSession($user->id);

        return TRUE;
    }

    public function logOut()
    {
        if($this->currentUserDetect() === NULL)
            return FALSE;

        $this->dropCurrentSession();
        return TRUE;
    }

    public function register(?string $username, ?string $password, ?string $email)
    {
        if($this->currentUserDetect() !== NULL)
            return FALSE;

        // нельзя создать юзера с занятым никнеймом
        if(UserModel::getByUsername($username) === NULL)
            return FALSE;

        // нельзя создать юзера с занятым емейлом
        if(UserModel::getByEmail($username) === NULL)
            return FALSE;

        $user = new UserModel([
            'name' => $username,
            'email' => $email,
            'password' => $password
        ]);

        return $user->insert() !== NULL;
    }

    private function makeUserSession(int $id)
    {
        $CS = CubSystem::getInstance();

        /* User Agent пользователя */
        $uua = $_SERVER['HTTP_USER_AGENT'];
        $uua = CsSecurity::hash($uua, TRUE, 'sha512');

        /* ID пользователя */
        $uid = CsSecurity::filter($id, 'int');

        /* IP адрес */
        $uip = $_SERVER['REMOTE_ADDR'];
        $uip = CsSecurity::hash($uip, TRUE, 'sha512');

        /* Персональный токен */
        $token = CsSecurity::rndStr(32, TRUE, TRUE, TRUE);
        $token = CsSecurity::hash($token, TRUE, 'sha512');

        /* Проверка на пустоту значений */
        if(empty_val($uua, $uid, $uip, $token))
            return;

        $CS->session->push('auth_uua', $uua);
        $CS->session->push('auth_uid', $uid);
        $CS->session->push('auth_uip',  $uip);
        $CS->session->push('auth_token', $token);
    }

    private function dropCurrentSession()
    {
        $CS = CubSystem::getInstance();
        $CS->session->purge('auth_uua');
        $CS->session->purge('auth_uip');
        $CS->session->purge('auth_uid');
        $CS->session->purge('auth_token');
    }

    private function currentUserDetect() : ?UserModel
    {
        $CS = CubSystem::getInstance();

        // агент юзера
        $uua = $CS->session->get('auth_uua');
        $uua = CsSecurity::filter($uua, 'sha512');

        // ip юзера
        $uip = $CS->session->get('auth_uip');
        $uip = CsSecurity::filter($uip, 'sha512');

        // id юзера
        $uid = $CS->session->get('auth_uid');
        $uid = CsSecurity::filter($uid, 'int');

        // token
        $token = $CS->session->get('auth_token');
        $token = CsSecurity::filter($token, 'sha512');

        /* Проверка на пустоту значений */
        if(empty_val($uua, $uid, $uip, $token))
            return NULL;

        // check data
        if($uua !== CsSecurity::hash($_SERVER['HTTP_USER_AGENT'], TRUE, 'sha512') ||
           $uip !== CsSecurity::hash($_SERVER['REMOTE_ADDR'], TRUE, 'sha512'))
        {
            return NULL;
        }

        $CS->info->setOption('security_CSRF-secure_token', $token, TRUE);

        return UserModel::getById($uid);
    }
}