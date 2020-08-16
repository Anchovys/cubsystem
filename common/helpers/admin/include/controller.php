<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
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
        $templatePanel = new CsTmpl('admin/panel', $CS->template);
        $templatePanel->set('cache_size', round(CsFS::folderSize(CS_CACHEPATH) / 1024, 3) . ' kB')
            ->set('options_size', round(CsFS::folderSize(CS_DATAPATH) / 1024, 3) . ' kB')
            ->set('logs_size', '0')
            ->set('core_version', '1.0')
            ->set('system_version', $CS->info->getOption('system')['version'])
            ->set('update_date', '?')
            ->set('helpers_count', count($CS->helpers->getLoaded()))
            ->set('helpers_list', implode(", ", array_keys($CS->helpers->getLoaded())))
            ->set('modules_count', count($CS->modules->getLoaded()))
            ->set('modules_list', implode(", ", array_keys($CS->modules->getLoaded())));

        $CS->template->getMainTmpl()
            ->set('content', $templatePanel->out());
    }

    public function Modules()
    {
        $CS = CubSystem::getInstance();

        if($param = CsSecurity::checkPost(['type', 'module', 'action']))
        {
          $type = default_val_array($param, 'type');
          $modules = default_val_array($param, 'module');
          $action = default_val_array($param, 'action');

          if(!empty_val($type, $modules, $action))
          {

            if($type ===  'loaded')
            {

              if($action === 'delete')
              {

                $loaded_mods = $CS->shared->getTextData('modules', TRUE);
                $loaded_mods = json_decode($loaded_mods, true);
                if(!is_array($loaded_mods)) $loaded_mods = [];

                foreach ($modules as $key => $value)
                {
                    if(in_array($value, $loaded_mods))
                    {
                      $CS->modules->purgeOnce($value);
                      unset($loaded_mods[$key]);
                    }
                }

                // переиндексируем массив
                $loaded_mods = array_values($loaded_mods);

                $CS->shared->saveTextData('modules', json_encode($loaded_mods), TRUE);

              }
              else if($action === 'disable')
              {
                $loaded_mods = $CS->shared->getTextData('modules', TRUE);
                $loaded_mods = json_decode($loaded_mods, true);
                if(!is_array($loaded_mods)) $loaded_mods = [];

                foreach ($modules as $key => $value)
                {
                    if(in_array($value, $loaded_mods))
                      unset($loaded_mods[$key]);
                }

                // переиндексируем массив
                $loaded_mods = array_values($loaded_mods);

                $CS->shared->saveTextData('modules', json_encode($loaded_mods), TRUE);

              }

            }
            else if($type === 'unloaded')
            {

              if($action === 'enable')
              {
                  $loaded_mods = $CS->shared->getTextData('modules', TRUE);
                  $loaded_mods = json_decode($loaded_mods);
                  if(!is_array($loaded_mods)) $loaded_mods = [];

                  foreach ($modules as $key => $value)
                  {
                      if(!in_array($value, $loaded_mods))
                        $loaded_mods[] = $value;
                  }

                    // переиндексируем массив
                    $loaded_mods = array_values($loaded_mods);

                    $CS->shared->saveTextData('modules', json_encode($loaded_mods), TRUE);
                  }


            }
          }
          header("Refresh:0");
        }

        $loaded_modules = [];
        $unloaded_modules = [];

        foreach ($CS->modules->getLoaded() as $module)
        {
            // значит загруженный
            if(is_object($module))
            {

              // получаем реальное имя - убираем префикс, то есть
              // $real_name = (module_default => default)
              $real_name = default_val_array(explode('_', $module->classname), 1);

              // принимаем значения из массива config модуля
              $config = $module->config;
              $name = CsSecurity::filter($config['name']);
              $ver = CsSecurity::filter($config['min_rev']);
              $desc = CsSecurity::filter($config['desc']);

              // формируем заголовок и описание
              $title = $name ? $name : 'no-name';
              $desc = $desc . ($ver ? ' (for version: ' . $ver . ')' : '');

              // если true, то модуль системный
              $is_system_module = in_array($real_name, $CS->config->getOption(['modules', 'autoload']));

              $loaded_modules[$real_name] =
              [
                'title'  =>  $title,
                'descr'  =>  $desc,
                'system' =>  $is_system_module
              ];

            } else {
              // получаем реальное имя - убираем префикс, то есть
              // $real_name = (module_default => default)
              $real_name = default_val_array(explode('_', $module['full_name']), 1);

              // принимаем значения из массива config модуля
              $config = $module['config'];
              $name = CsSecurity::filter($config['name']);
              $ver = CsSecurity::filter($config['min_rev']);
              $desc = CsSecurity::filter($config['desc']);

              // формируем заголовок и описание
              $title = $name ? $name : 'no-name';
              $desc = $desc . ($ver ? ' (for version: ' . $ver . ')' : '');

              // если true, то модуль системный
              $is_system_module = in_array($real_name, $CS->config->getOption(['modules', 'autoload']));

              $unloaded_modules[$real_name] =
              [
                'title'  =>  $title,
                'descr'  =>  $desc,
                'system' =>  $is_system_module
              ];

            }
        }

        $modulesTemplate = new CsTmpl('admin/modules', $CS->template);

        $modulesTemplate->set('loaded_modules', $loaded_modules);
        $modulesTemplate->set('unloaded_modules', $unloaded_modules);

        $CS->template->getMainTmpl()->set('content', $modulesTemplate->out());
    }

    public function Settings()
    {
        $CS = CubSystem::getInstance();
        $CS->template->getMainTmpl()->set('content', "Пока что, CubSystem не умеет изменять свои настройки в динамическом режиме.<br>Поэтому здесь пока ничего нет.");
    }
}
