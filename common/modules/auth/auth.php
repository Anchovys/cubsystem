<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| auth.php [rev 1.3], Назначение: система авторизации для пользователей
| -------------------------------------------------------------------------
| В этом файле описаны основные функциональности для работы
| с авторизацией пользователей
|
|
@
@   Cubsystem CMS, (с) 2020
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class auth_module extends cs_module
{
    private $currentUser = NULL;
    private $errors =      [];

    function __construct()
    {
        $this->name = "Auth";
        $this->description = "A simple authorization system.";
        $this->version = "3";
        $this->fullpath = CS_MODULESCPATH . 'auth' . _DS;
        $this->config['autoload'] = TRUE;
    }

    // on load module
    public function onLoad()
    {
        global $CS;

        // require default objects
        require_once($this->fullpath . 'objects' . _DS . 'user.php');

        // detect current logged user by session
        $this->currentUser = $this->_getCurrentUser();

        if($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__pre-template_hook', 'view', $this);
    }

    // on unload module
    public function onUnload()
    {

    }

    // on system install
    public function onInstall()
    {

    }

    // on enable that module
    public function onEnable()
    {

    }

    // on disable that module
    public function onDisable()
    {

    }

    public function view()
    {
        global $CS;
        $segments = cs_get_segment();

        if(isset($segments[1]) && $segments[0] === 'ajax')
        {
            $this->authHandler($segments);
            die();
        }

        switch(isset($segments[0]) ? $segments[0] : '') {
            case 'login':

                // уже авторизован
                if($this->currentUser !== NULL)
                    cs_redir('logout');

                $CS->template->setBuffer('body', $CS->template->callbackLoad([], 'auth/loginform_view'), FALSE);
                $CS->template->setMeta([
                    'title' => "Authorization",
                    'description' => "You can auth with you login/password"
                ]);
                break;
            case 'register':

                // уже авторизован
                if($this->currentUser !== NULL)
                    cs_redir('logout');

                $CS->template->setBuffer('body', $CS->template->callbackLoad([], 'auth/registerform_view'), FALSE);
                $CS->template->setMeta([
                    'title' => "Registration",
                    'description' => "You can register on website"
                ]);
                break;
            case 'logout':

                // не авторизован
                if($this->currentUser === NULL)
                    cs_redir('login');

                $CS->template->setBuffer('body', $CS->template->callbackLoad($this->currentUser, 'auth/logout_view'), FALSE);
                $CS->template->setMeta([
                    'title' => "Logout",
                    'description' => "Do you want logout?"
                ]);

                break;
        }
    }

    protected function authHandler($segments)
    {
        ///////////////////////////////////////////////////////////////
        ///// Обработка оболочки. Сюда должен приходить AJAX/POST /////
        ///////////////////////////////////////////////////////////////

        // простая защита от перебора пароля(задержка)
        sleep(3);

        $action = cs_filter($segments[1], 'special_string');

        if($action === 'login')
        {
            $usernameOrEmail = cs_filter($_POST['username']);
            $password        = cs_filter($_POST['password']);

            if(!$usernameOrEmail && !$password)
                return $this->_setError('no-data', TRUE);

            $msg = $this->_sentResponse($this->_auth($usernameOrEmail, $password));

        } elseif($action === 'register')
        {
            $username = cs_filter($_POST['username']);
            $password = cs_filter($_POST['password']);
            $email    = cs_filter($_POST['email'], 'base;email');

            if(!$username && !$password)
                return $this->_setError('no-data', TRUE);

            if(!$email)
                return $this->_setError('wrong email', TRUE);

            $msg = $this->_sentResponse($this->_register($username, $password, $email));
        }
        elseif($action == 'logout')
        {
            $this->_sentResponse($this->_purgeSession());
            cs_redir('', TRUE);
        }

        else return;

        // отправляем сообщение, если есть
        if(isset($msg)) die($msg);
    }

    public function getLoggedUser()
    {
        return $this->currentUser;
    }

    public function loggedIn()
    {
        return $this->currentUser !== FALSE;
    }

    protected function _auth($usernameOrEmail, $password)
    {
        // проверка что пришло: юзернейм или емейл
        if(cs_filter($usernameOrEmail, 'email'))
            $email = cs_filter($usernameOrEmail, 'base');
        else $username = cs_filter($usernameOrEmail, 'username');

        // filter by password
        $password = cs_filter($password, 'password');

        // выбираем по юзернейму или емейл
        if(isset($username))
            $user = cs_user::getByUsername($username);
        elseif(isset($email))
            $user = cs_user::getByEmail($email);

        if($user === NULL || !$password || !$user->checkPassword($password))
            return $this->_setError('incorrect');

        return $this->_makeSession($user->id);
    }

    protected function _register($username, $password, $email)
    {
        if(!$username || !$password || !$email)
            return $this->_setError('data?');

        $user = new cs_user (
            [
                'name'      =>  $username,
                'password'  =>  $password,
                'email'     =>  $email,
                'faction'   =>  0
            ]
        );

        // check user not exits in table
        if(cs_user::getByUsername($user->name,['id']) !== NULL ||
           cs_user::getByEmail($user->email,['id']) !== NULL)
            return $this->_setError('user-exists');

        // пробуем вставить данные
        $user = $user->insert();

        //не удалось
        if($user === NULL)
            return $this->_setError('failed-registration');

        return $this->_makeSession($user->id);
    }

    protected function _purgeSession()
    {
        if($this->currentUser === NULL) // already dont have session
            return $this->_setError('no-login');

        global $CS;
        $session = $CS->session;
        if(!$session)
            $this->_setError();

        return $session->purge('auth_uid') &&
               $session->purge('auth_uua') &&
               $session->purge('auth_uip');
    }


    protected function _makeSession($id)
    {
        if($this->currentUser !== NULL) // already have session
            return $this->_setError('already-login');

        global $CS;

        $session = $CS->session;
        if(!$session || !$id)
            $this->_setError();

        $id = intval($id);

        return $session->push('auth_uid', $id) &&
               $session->push('auth_uua', cs_hash_str($_SERVER['HTTP_USER_AGENT'])) &&
               $session->push('auth_uip', cs_hash_str($_SERVER['REMOTE_ADDR']));
    }

    protected function _getCurrentUser() // get current logged user from session
    {
        global $CS;

        $db         = $CS->database;
        $session    = $CS->session;

        if(!$db || !$session)
            $this->_setError();

        $u_id = $session->get('auth_uid');
        $u_id = cs_filter($u_id, 'int');

        $u_ua = $session->get('auth_uua');
        $u_ua = cs_filter($u_ua, 'base;string;sha512');

        $u_ip = $session->get('auth_uip');
        $u_ip = cs_filter($u_ip, 'base;string;sha512');

        // проверяем userAgent, IP-адрес клиента
        if( $u_ua !== NULL && $u_ua && $u_ua === cs_hash_str($_SERVER['HTTP_USER_AGENT']) &&
            $u_ip !== NULL && $u_ip && $u_ip === cs_hash_str($_SERVER['REMOTE_ADDR']))
        {
            return cs_user::getById($u_id, ['id', 'name', 'faction']);
        }
        return NULL;
    }

    protected function _setError($error = 'system', $die = TRUE)
    {
        $this->errors[] = cs_filter($error, 'string;spaces');

        if($die === TRUE)
            die($this->_sentResponse(FALSE));
        else return FALSE;
    }

    protected function _sentResponse($status = FALSE)
    {
        return json_encode([
            'status' => $status ? 1 : 0,
            'errors' =>
                [
                    'count' => count($this->errors),
                    'list'  => $this->errors,
                ]
        ]);
    }
}