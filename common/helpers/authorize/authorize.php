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
                if($post = CsSecurity::checkPost(['username', 'password']))
                {
                    $username = CsSecurity::filter($post['username'], 'username');
                    $password = CsSecurity::filter($post['password'], 'password');

                    /* Задержка 3 секунды - простая защита от перебора */
                    sleep(3);
                    $login = $this->logIn($username, $password);

                    CsUrl::redir($_SERVER['HTTP_REFERER'], FALSE);
                }
            });

            $CS->ajax->handle('register', function () {
                if($post = CsSecurity::checkPost(['username', 'password', 'email']))
                {
                    $username = CsSecurity::filter($post['username'], 'username');
                    $password = CsSecurity::filter($post['password'], 'password');
                    $email = CsSecurity::filter($post['email'], 'email');

                    /* Задержка 3 секунды - простая защита от перебора */
                    sleep(3);
                    $register = $this->register($username, $password, $email);

                    CsUrl::redir($_SERVER['HTTP_REFERER'], FALSE);
                }
            });

            $CS->ajax->handle('logout', function () {

                if($post = CsSecurity::checkPost(['token']))
                {
                    if(!CsSecurity::checkCSRFToken($post['token']))
                        die('Invalid token');

                    $logout = $this->logOut();
                }
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