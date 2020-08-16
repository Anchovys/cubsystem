<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */
?>
<form class="" method="post">
  <input type="hidden" name="type" value="loaded" />
  <div class="jumbotron jumbotron-fluid">
    <div class="container">
      <h1 class="display-4">Включённые модули <span class="badge badge-primary">{{ count($loaded_modules) }}</span></h1>
      <p class="lead">Здесь вы видите все загруженные модули в CubSystem.</p>
      <p class="lead">Будьте внимательны, когда делаете что-то здесь.</p>
      <p>
          <table class="table">
            {%  foreach ($loaded_modules as $key => $value): %}
              <tr>
                <td>
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">

                            {% if($value['system']): %}
                              <span class="badge badge-dark">
                                <input type="checkbox" checked disabled />
                              </span>
                            {% endif; %}

                            {% if(!$value['system']): %}
                              <span class="badge badge-primary">
                                <input type="checkbox" name="module[]" value="{{ $key }}" />
                              </span>
                            {% endif; %}

                            {{ $value['title'] }}

                          </h5>
                          <p class="card-text">{{ $value['descr'] }}</p>
                      </div>
                  </div>
                </td>
                <td></td>
              </tr>
            {% endforeach; %}
          </table>
      </p>
      <p class="lead">
        <input type="checkbox" required />
        Выполнить действия (для выбранных):
        <button type="submit" name="action" value="disable" class="btn btn-warning">Отключить</button>
        <button type="submit" name="action" value="delete" class="btn btn-danger">Отключить и удалить данные</button>
      </p>
      <p class="lead">Системные модули нельзя отключить здесь, но их всегда можно отключить в файле <code>/common/config/default.cfg.php</code>. </p>
    </div>
  </div>
</form>

<form class="" method="post">
  <input type="hidden" name="type" value="unloaded" />
  <div class="jumbotron jumbotron-fluid">
    <div class="container">
      <h1 class="display-4">Отключённые модули <span class="badge badge-primary">{{ count($unloaded_modules) }}</span></h1>
      <p class="lead">Здесь вы видите все не загруженные модули в CubSystem</p>
      <p>
          <table class="table">
            {%  foreach ($unloaded_modules as $key => $value): %}
              <tr>
                <td>
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">

                            <span class="badge badge-primary">
                              <input type="checkbox" name="module[]" value="{{ $key }}" />
                            </span>

                            {{ $value['title'] }}

                          </h5>
                          <p class="card-text">{{ $value['descr'] }}</p>
                      </div>
                  </div>
                </td>
                <td></td>
              </tr>
            {% endforeach; %}
          </table>
      </p>
      <p class="lead">
        <input type="checkbox" required />
        Выполнить действия (для выбранных):
        <button type="submit" name="action" value="enable" class="btn btn-danger">Включить/Инсталлировать</button>
      </p>
    </div>
  </div>
</form>
