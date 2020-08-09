<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

?>
<div class="row">
    <div class="col-sm-6">
        <div class="card rounded-0">
            <div class="card-body">
                <h5 class="card-title">Данные системы</h5>
                <div class="card-text">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th scope="row">Кеш системы</th>
                            <td>{? $cache_size ?}</td>
                        </tr>
                        <tr>
                            <th scope="row">Данные опций</th>
                            <td>{? $options_size ?}</td>
                        </tr>
                        <tr>
                            <th scope="row">Логи</th>
                            <td colspan="2">{? $logs_size ?}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <a href="#" class="btn btn-primary">Сбросить кеш</a>
                <a href="#" class="btn btn-primary">Полный сброс</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card rounded-0">
            <div class="card-body">
                <h5 class="card-title">Версия системы</h5>
                <div class="card-text">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th scope="row">Версия ядра</th>
                            <td>{? $core_version ?}</td>
                        </tr>
                        <tr>
                            <th scope="row">Версия системы</th>
                            <td>{? $system_version ?}</td>
                        </tr>
                        <tr>
                            <th scope="row">Последнее обновление</th>
                            <td colspan="2">{? $update_date ?}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <a href="#" class="btn btn-primary">Запросить обновление</a>
            </div>
        </div>
    </div>
</div>
