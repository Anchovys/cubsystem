<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class admin_menu
{
    private array $menuItems = [];
    public function add($name, $url) {
        $this->menuItems [$url] = $name;
    }
    public function getHtml()
    {
        $total = '';

        foreach ($this->menuItems as $url=>$name)
            $total .= "<a class='nav-link' href='" . CsUrl::absUrl('admin/' . $url) . "'>$name</a>";
        return $total;
    }
}