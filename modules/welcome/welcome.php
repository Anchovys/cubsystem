<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class module_welcome extends CsModule
{
    public function onLoad()
    {
        $CS = CubSystem::getInstance();
        $CS->hooks->register('system_print_tmpl', function () use(&$CS)
        {
            $template = $CS->template;
            $template->setMeta('title', 'Hello! Welcome to CubSystem!');
            $template->setMeta('css', 'bootstrap.min.css');
            $template->setMeta('js', 'bootstrap.min.js');

            $mainTmpl = $template->getMainTmpl();
            $mainTmpl->set('title', 'Welcome to');
            $mainTmpl->set('subtitle', 'CubSystem minimal');

            $modules_buffer = '';
            foreach ($CS->modules->getLoaded(null, true) as $module)
            {
                // принимаем значения из config
                $config = $module->config;
                $name = CsSecurity::filter($config['name']);
                $ver = CsSecurity::filter($config['min_rev']);
                $desc = CsSecurity::filter($config['desc']);

                // формируем заголовок и описание
                $title = $name ? $name : 'no-name';
                $desc = $desc . ($ver ? ' (for version: ' . $ver . ')' : '');

                $t = new CsTmpl('blocks/basic/card', $template);
                $modules_buffer = $modules_buffer . $t // добавляем данные
                        ->set('card_title',  $title)
                        ->set('card_text', $desc)
                        ->set('custom_card_class', 'border-0')
                        ->out(); // и выводим
            }

            // ставим в буфер модуля получившуюся переменную
            $mainTmpl->set('modules', $modules_buffer);
        });

        return parent::onLoad();
    }
}