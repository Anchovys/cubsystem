<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class images_module extends cs_module
{
    function __construct()
    {
    }

    // on load module
    public function onLoad()
    {
        global $CS;

        define("CS_UPLOADSPATH", CS__BASEPATH . 'uploads' . _DS);

        if($h = $CS->gc('hooks_helper', 'helpers'))
        {
            // admin hooks
            $h->register('cs__admin-view', 'admin_view', $this);
            $h->register('cs__admin-ajax', 'admin_ajax', $this);
        }
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

    function admin_view()
    {
        global $CS;
        if(cs_get_segment(1) == 'uploads')
        {
            $CS->template->callbackLoad('', 'images/uploads_view', 'body');
        }
    }

    function admin_ajax()
    {

    }
}