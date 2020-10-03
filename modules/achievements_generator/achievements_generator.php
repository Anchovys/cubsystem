<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

class module_achievements_generator extends CsModule {

    public function onLoad()
    {
        $CS = CubSystem::getInstance();

        $main_route = $this->config['route']['root'];
        $generate_route = $main_route . $this->config['route']['generate'];
        $get_icons_route = $main_route . $this->config['route']['get_icons'];

        require_once ($this->directory . "common" . _DS . 'controller.php');
        require_once ($this->directory . "common" . _DS . 'generator.php');

        $controller = new achievement_generator\Controller();
        $CS->router->all($main_route, function ($next) use ($controller) {
            $controller->$next();
        });

        return parent::onLoad();
    }

}