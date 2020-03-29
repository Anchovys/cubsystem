<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| auth.php [rev 1.2], Назначение: система авторизации для пользователей
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
        global $CS;
        $this->name = "Auth";
        $this->description = "A simple authorization system.";
        $this->version = "2";
        $this->fullpath = CS__MODULESPATH . 'auth' . _DS;

        // require default objects
        require_once($this->fullpath . 'objects' . _DS . 'user.php');

        // detect current logged user by session
        $this->currentUser = $this->getCurrentUser();

        if($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__post-modules_hook', 'authHandler', $this);
    }

    public function authHandler()
    {
        $segments = cs_get_segments();

        // nothing to do
        if($segments[0] !== 'authorize-shell' || !isset($segments[1]))
            return;

        $action = cs_filter($segments[1], 'special_string');

        if($action === 'login')
        {
            $username = cs_filter($_POST['username']);
            $password = cs_filter($_POST['password']);

            if(!$username && !$password)
                $this->setError('no-data', TRUE);

            $this->sentResponse($this->auth($username, $password));

        } elseif($action === 'register')
        {
            $username = cs_filter($_POST['username']);
            $password = cs_filter($_POST['password']);

            if(!$username && !$password)
                $this->setError('no-data', TRUE);

            $this->sentResponse($this->register($username, $password));
        }
        elseif($action == 'logout')
        {
            $this->sentResponse($this->purgeSession());

            //    cs_redir(base64_decode($_GET['next']), false);
        }
        else return;
    }

    public function getLoggedUser()
    {
        return $this->currentUser;
    }

    public function loggedIn()
    {
        return $this->currentUser !== FALSE;
    }

    private function auth($username, $password, $email = '')
    {
        // filter by username
        $username = cs_filter($username, 'username');

        // filter by password
        $password = cs_filter($password, 'password');

        $user = cs_user::getByUsername($username);

        if($user === NULL || !$user->checkPassword($password))
            $this->setError('incorrect');

        return $this->makeSession($user->id);
    }

    private function register($username, $password, $email = '')
    {
        $user = new cs_user (
            [
                'name'      =>  $username,
                'password'  =>  $password,
                'faction'   =>  0
            ]
        );
        $user = $user->insert();

        if($user === NULL)
            $this->setError('failed-registration');

        return $this->makeSession($user->id);
    }

    private function purgeSession()
    {
        if($this->currentUser === NULL) // already dont have session
            return $this->setError('no-login');

        global $CS;
        $session = $CS->session;
        if(!$session)
            $this->setError();

        return $session->purge('auth_uid') &&
               $session->purge('auth_uua') &&
               $session->purge('auth_uip');
    }


    private function makeSession($id)
    {
        if($this->currentUser !== NULL) // already have session
            return $this->setError('already-login');

        global $CS;

        $session = $CS->session;
        if(!$session || !$id)
            $this->setError();

        $id = intval($id);

        return $session->push('auth_uid', $id) &&
               $session->push('auth_uua', cs_hash_str($_SERVER['HTTP_USER_AGENT'])) &&
               $session->push('auth_uip', cs_hash_str($_SERVER['REMOTE_ADDR']));
    }

    private function getCurrentUser() // get current logged user from session
    {
        global $CS;

        $db         = $CS->database;
        $session    = $CS->session;

        if(!$db || !$session)
            $this->setError();

        $u_id = $session->get('auth_uid');
        $u_id = cs_filter($u_id, 'int');

        $u_ua = $session->get('auth_uua');
        $u_ua = cs_filter($u_ua, 'base;string;md5');

        $u_ip = $session->get('auth_uip');
        $u_ip = cs_filter($u_ip, 'base;string;md5');

        // проверяем userAgent, IP-адрес клиента
        if( $u_ua !== NULL && $u_ua && $u_ua === cs_hash_str($_SERVER['HTTP_USER_AGENT']) &&
            $u_ip !== NULL && $u_ip && $u_ip === cs_hash_str($_SERVER['REMOTE_ADDR']))
        {
            return cs_user::getById($u_id);
        }
        return NULL;
    }

    private function setError($error = 'system', $die = TRUE)
    {
        $this->errors[] = cs_filter($error, 'string;spaces');

        if($die === TRUE)
            die($this->sentResponse(FALSE));
        else return FALSE;
    }

    private function sentResponse($status = FALSE)
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
?>