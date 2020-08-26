<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

?>

<div class="jumbotron">
    <h1 class="display-4">Добавление страницы</h1>
    <p class="lead">Редактируйте и создавайте свои страницы здесь!</p>
    <form method="post" action="{{CsUrl::pathToUrl('ajax_handler/sb_page') }}">
        {% if(isset($id)): %}
            <input type="hidden" name="id" value="{{$id}}">
        {% endif; %}
        <input type="hidden" name="token" value="{{ $token }}">
        <p>
            <div style="min-width: 100%;">
                <input type="text" name="title" class="form-control" placeholder="Заголовок записи">
            </div>
            <textarea name="content" id="editor" class="form-control" style="min-width: 100%; height: 600px;" placeholder="Начните писать здесь..."></textarea>
            <input type="submit" value="Save" class="btn" />
        </p>
    </form>
</div>

