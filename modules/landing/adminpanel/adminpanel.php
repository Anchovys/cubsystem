<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class LandingPageAdmin
{
    private module_landing $landing;
    public function __construct($landing)
    {
        $this->landing = $landing;
    }

    public function init($path)
    {
        $CS = CubSystem::getInstance();
        $CS->admin->menu->add('Landing', 'landing');

        $landing = $this->landing;

        $CS->admin->setAction('landing', function () use ($CS, $landing, $path) {

            $pages = $landing->registeredPages;

            switch (CsUrl::segment(2))
            {
                case "addpage":

                    $buffer = $CS->template->handleFile($path . 'tmpl'. _DS . 'editor.php', ['pages'=>$pages]);
                    $CS->template->getMainTmpl()->set('content', $buffer);

                    break;
                case FALSE:
                    die('Home');
                    break;
            }
        });
    }
}