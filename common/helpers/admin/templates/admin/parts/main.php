<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>
<header class="container bg-light">
    <div class="row">
        <div class="col-6">
            <div class="media">
                <img src="{?CsUrl::pathToUrl($this->directory)?}assets/img/logo.png" width="75" height="75" class="mr-3">
                <div class="media-body">
                    <h3 style="margin-bottom: 0px;">CubSystem</h3>
                    <small>Version {? $CS->info->getOption('system')['version'] ?} (dev)</small>
                </div>
            </div>
        </div>
        <div class="col-6">
            {? $authbar ?}
        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            {? $menu ?}
        </div>
    </div>
</header>
<div class="container bg-light">
    {? $content ?}
    <p class="mt-5 mb-3 text-muted">&copy; 2020, Anchovy, powered on CubSystem</p>
</div>