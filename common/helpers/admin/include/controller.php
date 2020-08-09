<?php
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license.
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class CsAdminController
{
    public function Panel()
    {
        $CS = CubSystem::getInstance();

        $template = new CsTmpl('admin/panel', $CS->template);
        $template->set('cache_size', '50 MB');
        $template->set('options_size', '10 MB');
        $template->set('logs_size', '0 MB');
        $template->set('core_version', 'same');
        $template->set('system_version', $CS->info->getOption('system')['version']);
        $template->set('update_date', '?');
        $CS->template->getMainTmpl()->set('content', $template->out());
    }

    public function Modules()
    {
        $CS = CubSystem::getInstance();

        $modules_buffer = '';
        foreach ($CS->modules->getLoaded() as $module)
        {
            if(!is_object($module)) continue;
            // принимаем значения из config
            $config = $module->config;
            $name = CsSecurity::filter($config['name']);
            $ver = CsSecurity::filter($config['min_rev']);
            $desc = CsSecurity::filter($config['desc']);

            // формируем заголовок и описание
            $title = $name ? $name : 'no-name';
            $desc = $desc . ($ver ? ' (for version: ' . $ver . ')' : '');

            $t = new CsTmpl('blocks/basic/card', $CS->template);
            $modules_buffer = $modules_buffer . $t // добавляем данные
                ->set('card_title',  $title)
                    ->set('card_text', $desc)
                    ->set('custom_card_class', 'border-0')
                    ->out(); // и выводим
        }

        $CS->template->getMainTmpl()->set('content', $modules_buffer);
    }

    public function Settings()
    {
        $CS = CubSystem::getInstance();
        $CS->template->getMainTmpl()->set('content', "Пока что, CubSystem не умеет изменять свои настройки в динамическом режиме.<br>Поэтому здесь пока ничего нет.");
    }
}