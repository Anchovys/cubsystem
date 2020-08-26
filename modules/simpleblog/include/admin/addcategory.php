<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

?>

<div class="jumbotron">
    <h1 class="display-4">Категории</h1>
    <p class="lead">Редактирование / добавление категории</p>
    <form method="post" action="{{CsUrl::pathToUrl('ajax_handler/sb_cat') }}">
        {% if(isset($id)): %}
            <input type="hidden" name="id" value="{{$id}}">
        {% endif; %}
        <input type="hidden" name="token" value="{{$token}}">
        <p>
        <div style="min-width: 100%;">
            <input type="text" name="name" class="form-control" placeholder="Название категории" value="{? $cat_name ?}">
            <input type="text" name="slug" class="form-control" placeholder="Адрес в адресной строке (не обязательно)" value="{? $cat_slug ?}" >
        </div>
        <textarea name="description" id="editor" class="form-control" style="min-width: 100%; height: 100px;" placeholder="Описание категории (не обязательно)" >{? $cat_description ?}</textarea>
        <input type="submit" value="Save" class="btn" />
        </p>
    </form>
</div>

