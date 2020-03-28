<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| auth.php [rev 1.0], Назначение: система авторизации для пользователей
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
    public $currentUser = NULL;

    function __construct()
    {
        global $CS;
        $this->name = "Auth";
        $this->description = "A simple authorization system.";
        $this->version = "1";
        $this->fullpath = CS__MODULESPATH . 'auth' . _DS;

        // require default objects
        require_once($this->fullpath . 'objects' . _DS . 'user.php');

        $this->currentUser = $this->getCurrentUser();

        pr($this->currentUser);

        if($h = $CS->gc('hooks_helper', 'helpers'))
            $h->register('cs__post-modules_hook', 'authHandler', $this);
    }

    public function authHandler()
    {
        global $CS;
        $segments = cs_get_segments();

        if($segments[0] !== 'authorize-shell' || !isset($segments[1]))
            return;

        // nothing to do
        if(!isset($_POST))
            return;

        $action = $segments[1];

        if($action == 'login')
        {
            $username = cs_filter($_POST['username']);
            $password = cs_filter($_POST['password']);

            die($this->auth($username, $password) ? "ok" : "fail");

        } elseif($action == 'register')
        {
            $username = cs_filter($_POST['username']);
            $password = cs_filter($_POST['password']);

            die($this->register($username, $password) ? "ok" : "fail");
        }
        elseif($action == 'logout')
        {
            die($this->purgeSession() ? "ok" : "fail");

            //    cs_redir(base64_decode($_GET['next']), false);
        }
        else return;
    }

    private function auth($username, $password, $email = '')
    {
        if($this->currentUser !== NULL) // already have session
            return FALSE;

        // filter by username
        $username = cs_filter($username, 'username');

        $user = cs_user::getByNickname($username);

        if($user === NULL)
            return FALSE;

        if(!$user->checkPassword($password))
            return FALSE;

        return $this->makeSession($user->id);
    }

    private function register($username, $password, $email = '')
    {
        if($this->currentUser !== NULL) // already have session
            return FALSE;

        $user = new cs_user (
            [
                'name'      =>  $username,
                'password'  =>  $password,
                'faction'   =>  1
            ]
        );
        $user = $user->insert();

        if($user === NULL)
            return FALSE;

        return $this->makeSession($user->id);
    }

    private function purgeSession()
    {
        if($this->currentUser === NULL) // already dont have session
            return FALSE;

        global $CS;
        $session = $CS->session;
        if(!$session)
            return FALSE;

        return $session->push('auth_uid') &&
               $session->push('auth_uua') &&
               $session->push('auth_uip');
    }


    private function makeSession($id)
    {
        global $CS;

        $session = $CS->session;
        if(!$session || !$id)
            return FALSE;

        $id = intval($id);

        return $session->push('auth_uid', $id) &&
               $session->push('auth_uua', cs_hash_str($_SERVER['HTTP_USER_AGENT'])) &&
               $session->push('auth_uip', cs_hash_str($_SERVER['REMOTE_ADDR']));
    }

    private function getCurrentUser()
    {
        global $CS;

        $db = $CS->database;
        $session = $CS->session;

        if(!$db || !$session)
            return NULL;

        $u_id = (int)$session->get('auth_uid');
        $u_ua = $session->get('auth_uua');
        $u_ip = $session->get('auth_uip');

        // проверяем userAgent
        if( $u_ua === null || $u_ua !== cs_hash_str($_SERVER['HTTP_USER_AGENT']) ||
            $u_ip === null || $u_ip !== cs_hash_str($_SERVER['REMOTE_ADDR']))
        {
            return NULL;
        }
        return cs_user::getById($u_id);
    }
}
?>