<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>

<?php if($CS->auth->getCurrent() != null) { $user = $CS->auth->getCurrent() ?>

<div class="media">
    <img src="https://dummyimage.com/75x75/c7c7c7/000000.png&text=x" class="mr-3">
    <div class="media-body">
        <p class="mb-0">
        <h5 class="d-inline">{? $user->name ?}</h5> | <a href="{?CsUrl::absUrl('ajax_handler/logout/')?}">Logout</a>
        </p>
        Group: {{ $user->isAdmin() ? 'Admin' : 'Unknown'; }}
    </div>
</div>

<?php } ?>