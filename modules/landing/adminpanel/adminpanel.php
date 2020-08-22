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
                case "editor":
                    if(CsUrl::segment(3) == 'new')
                    {
                        $buffer = $CS->template->handleFile($path  . 'adminpanel' . _DS . 'tmpl'. _DS . 'editor-newpage.php', ['path'=>$path]);
                        $CS->template->getMainTmpl()->set('content', $buffer);
                    } else {
                        $buffer = $CS->template->handleFile($path  . 'adminpanel' . _DS . 'tmpl'. _DS . 'editor.php', ['pages'=>$pages]);
                        $CS->template->getMainTmpl()->set('content', $buffer);
                    }
                    break;
                case FALSE:
                    $buffer = $CS->template->handleFile($path  . 'adminpanel' . _DS . 'tmpl'. _DS . 'index.php');
                    $CS->template->getMainTmpl()->set('content', $buffer);
                    break;
            }
        });
    }
}
