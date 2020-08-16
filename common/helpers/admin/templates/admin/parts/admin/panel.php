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
                        </tbody>
                    </table>
                </div>
                <!--a href="#" class="btn btn-primary">Сбросить кеш</a-->
                <!--a href="#" class="btn btn-primary">Полный сброс</a-->
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
                        </tbody>
                    </table>
                </div>
                <!--a href="#" class="btn btn-primary" onclick="alert('Напишите Anchovy');">Запросить обновление</a-->
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card rounded-0">
            <div class="card-body">
                <h5 class="card-title">Хелперы ядра и модули</h5>
                <div class="card-text">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th scope="row">Хелперы ({? $helpers_count ?})</th>
                            <td>{? $helpers_list ?}</td>
                        </tr>
                        <tr>
                            <th scope="row">Модули ({? $modules_count ?})</th>
                            <td>{? $modules_list ?}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card rounded-0">
            <div class="card-body">
                <h5 class="card-title">Лог изменений | <a href="https://github.com/Anchovys/cubsystem">Исходники</a> на Github</h5>
                <div class="card-text">
                    {%
                        $CS = CubSystem::getInstance();
                        $output = $CS->cache->get('admin-panel_changelog_git');
                        if(!$output) {
                            $output = file_get_contents('https://raw.githubusercontent.com/Anchovys/cubsystem/master/changelog.txt');
                            $CS->cache->set('admin-panel_changelog_git', $output, 3600);
                        }
                        $output = CsSecurity::filter($output);
                    %}
                    <textarea class="form-control" style="height: 200px; font-family:monospace;">{? $output ?}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
