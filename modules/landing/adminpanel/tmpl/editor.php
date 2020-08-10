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
		<?php
		
			if(isset($_POST['file']))
			{
				$path = CS__BASEPATH . $pages[$_GET['file']];
				file_put_contents(CsSecurity::filter($path, 'path'), $_POST['file']);
			}
		
		?>
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
						$selected = ($name == $_GET['file']) ? 'selected' : '';
						print "<option value='$name' $selected>$page</option>";
					}
                ?>
            </select>
        </div>
        <form method="post">
			<textarea name="file" id="editor" class="form-control" style="min-width: 100%; height: 600px;">
				<?php
					$path = CS__BASEPATH . $pages[$_GET['file']];
					echo file_get_contents(CsSecurity::filter($path, 'path'));
				?>
			</textarea>
			<input type="submit" value="save" class="btn" />
		</form>
    </p>
</div>