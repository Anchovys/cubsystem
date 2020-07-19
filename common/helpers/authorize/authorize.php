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

    private ?CsUser $_currentUser;

    public function __construct()
    {
        require_once(CS_HELPERSPATH . 'authorize/UserModel1.php');
        $this->_currentUser = $this->currentUserDetect();
    }

    private function makeUserSession(int $id)
    {
        $CS = CubSystem::getInstance();

        $uua = $_SERVER['HTTP_USER_AGENT'];
        $uua = CsSecurity::hash($uua, TRUE, 'sha512');

        $uid = CsSecurity::filter($id, 'int');

        $uip = $_SERVER['REMOTE_ADDR'];
        $uip = CsSecurity::hash($uip, TRUE, 'sha512');

        $CS->session->push([
            'auth_uua' => $uua,
            'auth_uid' => $uid,
            'auth_uip' => $uip
        ]);
    }

    private function dropCurrentSession()
    {
        $CS = CubSystem::getInstance();
        $CS->session->purge('auth_uua');
        $CS->session->purge('auth_uip');
        $CS->session->purge('auth_uid');
    }

    private function currentUserDetect()
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

        // check data
        if($uua != CsSecurity::hash($_SERVER['HTTP_USER_AGENT'], TRUE, 'sha512') ||
           $uip != CsSecurity::hash($_SERVER['REMOTE_ADDR'], TRUE, 'sha512'))
        {
            return NULL;
        }
    }


}