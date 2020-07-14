<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class module_landing extends CsModule
{
    /**
     * Действия при загрузке модуля.
     * @return bool
     */
    public function onLoad()
    {
        $moduleConfig = $this->config['module'];

        /* устанавливаем директорию поиска */
        if ($moduleConfig['pages_path'])
            $seek_dir = CS__BASEPATH . $moduleConfig['pages_path'] . _DS;
        else $seek_dir = $this->directory . 'pages' . _DS;
        $seek_dir = CsSecurity::filter($seek_dir, 'path');

        /* Берем значение - глубина сканирования. По дефолту = 3 */
        $scan_depth = default_val($moduleConfig['scan_depth'], 3);

        /* Сканируем директорию с учетом глубины сканирования */
        $directories = CsFS::getDirectories($seek_dir, $scan_depth);

        /* Перебираем каждую директорию */
        foreach ($directories as $directory)
        {
            $full_directory = $directory . _DS; // полный путь к папке
            $half_directory = str_replace($seek_dir, '', $directory); // короткий путь (только название папки)
            unset($directory);

            if (!CsFS::dirExists($full_directory)) continue; // нет такой директории
            $filename = $full_directory . default_val($moduleConfig['index_file'], 'index.php');

            /* Проверка и регистрация главной страницы */
            if($moduleConfig['main_page'] && explode('/', $half_directory)[0] == $moduleConfig['main_page'])
                $this->registerHome($filename, str_replace($moduleConfig['main_page'], '', $half_directory));

            /* Регистрация страницы */
            $this->registerPage($half_directory, $filename);
        }

        return parent::onLoad();
    }


    private function registerHome($filename, $suffix = '')
    {
        $this->registerPage($suffix, $filename);
    }

    private function registerPage($name, $filename)
    {
        //pr("регистрирую $filename на '/$name'");
        $CS = CubSystem::getInstance();

        /* Определяем файл страницы */
        if ( !CsFS::fileExists($filename)) return FALSE;

        /* Вешаем на маршрут */
        $CS->router->get('/' . $name, function() use($filename, $CS)
        {
            /* Пробуем вешать хук перед выводом шаблона */
            $CS->hooks->register('system_print_tmpl', function() use($filename, $CS)
            {
                $string = $CS->template->handleFile($filename); // обработаем страницу
                $CS->template->mainId = 1;
                $CS->template->getMainTmpl()->
                    set('content', $string, 0);
            });
        });

        return TRUE;
    }
}