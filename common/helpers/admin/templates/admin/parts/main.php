<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>
<header class="container bg-light">
    <div class="row">
        <div class="col col-4">
            <div class="row">
                <div class="col col-3">
                    <img src="./img/logo.png" width="85" height="85" alt="">
                </div>
                <div class="col col-9">
                    <h1 style="margin-bottom: 0px;">CubSystem</h1>
                    <small>Version {? $CS->info->getOption('system')['version'] ?} (dev)</small>
                </div>
            </div>
        </div>
        <div class="col col-8">

        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="overflow-auto">
                    <div class="navbar-nav">
                        <a class="nav-link active" href="#">Панель</a>
                        <a class="nav-link" href="#">Настройки</a>
                        <a class="nav-link" href="#">Модули</a>
                        <div style="border-left: 2px solid #444;">
                            <div class="navbar-nav">
                                {? $addition_buttons ?}
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
<div class="container bg-light">
    {? $content ?}
</div>