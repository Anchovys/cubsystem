<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

?>

<div class="jumbotron">
    <h1 class="display-4">Модуль блог</h1>
    <p class="lead">Редактируйте и создавайте свои страницы здесь!</p>
    <div class="row">

        <div class="col-8">
            <h3>Список записей</h3>
            <a href="<?=CsUrl::absUrl('admin/s-blog/article')?>" class="btn btn-primary w-100">Создать новую запись</a>
            <?= $articles_list; ?>
        </div>

        <div class="col-4">
            <h3>Список категорий</h3>
            <a href="<?=CsUrl::absUrl('admin/s-blog/category')?>" class="btn btn-primary w-100">Добавить категорию</a>
            <?= $categories_list; ?>
        </div>

    </div>
</div>


