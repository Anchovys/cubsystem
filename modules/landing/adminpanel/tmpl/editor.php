<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>
<div class="jumbotron">
    <h1 class="display-4">Landing page editor</h1>
    <p class="lead">Edit your pages here in this editor!</p>
    <p>
        <script>
            function change(object) {
                let value = object.value;
                window.location.href = "?file=" + value;
            }
        </script>
        <div style="min-width: 100%;">
            <select name="" id="selectFile" class="form-control" onchange="change(this)">
                <?php
                    foreach ($pages as $name=>$page)
                        print "<option value='$page'>$name</option>";
                ?>
            </select>
        </div>
        <textarea id="editor" class="form-control" style="min-width: 100%; height: 600px;">
            <?php
                $path = CS__BASEPATH . $_GET['file'];
                $path = CsSecurity::filter($path, 'path');

                echo file_get_contents($path)
            ?>
        </textarea>
    </p>
</div>