<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

if($post = CsSecurity::checkPost([ 'name', 'file' ]))
{
    $name = CsSecurity::filter($post['name'], 'path');
    $name_arr = explode('/', $name);
    foreach ($name_arr as $key => $item)
    {
        $item = CsSecurity::filter($item, 'special_string');
        if(empty($item)) unset($name_arr[$key]);
        $name_arr[$key] = $item;
    }

    $name = $path . 'pages' . _DS . implode(_DS, $name_arr);
    $name = CsSecurity::filter($name, 'path') . _DS;

    $file = $post['file']; // TODO: Почему 777
    if(!CsFS::mkdirIfNotExists($name, 777, TRUE) || file_put_contents($name . 'index.php', $file) == false)
        $fail = true;
}
?>
<div class="jumbotron">
    <h1 class="display-4">Редактор страниц Landing</h1>
    <p class="lead">Редактируйте и создавайте свои страницы здесь!</p>
    {% if(isset($fail) && $fail): %}
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Внимание!</strong> Какая-то проблема произошла при добавлении страницы
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    {% endif; %}
    <p>
    <form method="post">
        <div style="min-width: 100%;">
            <input type="text" name="name" class="form-control"/>
        </div>
			<textarea name="file" id="editor" class="form-control" style="min-width: 100%; height: 600px;"><?="<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
.  @copyright Copyright (c) 2020, Anchovy.
.  @author Anchovy, <contact.anchovy@gmail.com>
.  @license MIT public license
.  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/* OPTIONS_BLOCK {
    \"title\": \"Hello, welcome to landing!\",
    \"css\": [\"bootstrap.min.css\"],
    \"desc\": \"My landing are beautiful\",
    \"keywords\": \"Landing, PHP, CubSystem\",
    \"tmpl_part\": \"blank\",
    \"content_buffer\": \"content\"
}*/
?>
<h1>
    Hello!
    Welcome to landing!
</h1>"?></textarea>
			<input type="submit" value="Add" class="btn" />
		</form>
    </p>
</div>
