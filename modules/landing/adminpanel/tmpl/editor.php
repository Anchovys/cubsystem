<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

$pageName = default_val_array($_GET, 'file', array_key_first($pages));

$pagePath = default_val_array($pages, $pageName);
$pagePath = CS__BASEPATH . $pagePath;
$pagePath = CsSecurity::filter($pagePath, 'path');

if(isset($_POST['file']))
{
    file_put_contents($pagePath, $_POST['file']);
}

?>
<div class="jumbotron">
    <h1 class="display-4">Редактор страниц Landing</h1>
    <p class="lead">Редактируйте и создавайте свои страницы здесь!</p>
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
                    foreach ($pages as $name => $page)
					{
						$selected = ($name == $pageName) ? 'selected' : '';
						print "<option value='$name' $selected>$page</option>";
					}
                ?>
            </select>
        </div>
        <form method="post">
			<textarea name="file" id="editor" class="form-control" style="min-width: 100%; height: 600px;"><?= file_get_contents($pagePath, 'path'); ?></textarea>
			<input type="submit" value="Save" class="btn" />
		</form>
    </p>
</div>
