<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>

{% if($CS->auth->getCurrent() !== NULL && !$CS->admin->hasAccess()) { %}
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">К сожалению, у вас нет прав.</h4>
        <p>Вы авторизованы, но у вас нет доступа к этой странице.
            Вы можете попробовать выйти и зайти под профилем администратора.</p>
    </div>
{% } %}
<form class="form-signin" method="post" action="{{CsUrl::pathToUrl('ajax_handler/login') }}">
    <h1 class="h3 mb-3 font-weight-normal">Пожалуйста, авторизуйтесь</h1>
    <label for="inputLogin" class="sr-only">Логин</label>
    <input type="text" id="inputLogin" class="form-control" name="username" placeholder="Логин" required autofocus>
    <label for="inputPassword" class="sr-only">Пароль</label>
    <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Пароль" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>
