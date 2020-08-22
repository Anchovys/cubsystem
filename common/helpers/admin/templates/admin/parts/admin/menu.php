<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>
<script>
    window.onload = function () {
        let menuChild = Array.from(document.getElementById('menu').children);
        menuChild.forEach(function (element) {
            if(element.tagName !== 'A') return;
            if(window.location.href.includes(element.href) &&
                window.location.href === element.href) {
                element.classList.add('active');
            } else element.classList.remove('active');
        });
    }
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="overflow-auto">
        <div class="navbar-nav" id="menu">
            <a class="nav-link active" href="{?CsUrl::absUrl('admin/panel/')?}">Панель</a>
            <a class="nav-link" href="{?CsUrl::absUrl('admin/settings/')?}">Настройки</a>
            <a class="nav-link" href="{?CsUrl::absUrl('admin/modules/')?}">Модули</a>
            <div style="border-left: 2px solid #444;"></div>
            {? $addition_buttons ?}
        </div>
    </div>
</nav>
