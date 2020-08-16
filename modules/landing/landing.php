<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class module_landing extends CsModule
{

    public array $registeredPages = [];

    /**
     * Действия при загрузке модуля.
     * @return bool
     */
    public function onLoad()
    {
        $CS = CubSystem::getInstance();
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

        if($CS->admin != null)
        {
            require_once($this->directory . 'adminpanel' . _DS . 'adminpanel.php');
            $adminpanelIntegrate = new LandingPageAdmin($this);
            $adminpanelIntegrate->init($this->directory);
        }
        return parent::onLoad();
    }

    private function registerHome($filename, $suffix = '')
    {
        $this->registerPage($suffix, $filename);
    }

    private function registerPage($name, $filename)
    {
        $CS = CubSystem::getInstance();

        /* Определяем файл страницы */
        if ( !CsFS::fileExists($filename)) return FALSE;

        /* Вешаем на маршрут */
        $CS->router->get( ('/' . $name) , function() use($filename, $CS)
        {
            /* Пробуем вешать хук перед выводом шаблона */
            $CS->hooks->register('system_print_tmpl', function() use($filename, $CS)
            {
                // получим опции из файла
                $options = $this->getOptions($filename);

                // перерегистрируем шаблон
                if(is_string(default_val($options['template'], FALSE)) && $CS->template !== NULL)
                {
                    $CS->template = $CS->template->register($options['template']);
                }

                // выставляем мета-поля
                if($options['title']) $CS->template->setMeta('title', $options['title']);
                if($options['desc']) $CS->template->setMeta('description', $options['desc']);
                if($options['keywords']) $CS->template->setMeta('keywords', $options['keywords']);

                // выставляем поля css
                if($options['css'] && is_string($options['css']))
                    $CS->template->setMeta('css', $options['css']);
                else if(is_array($options['css'])) foreach ($options['css'] as $css)
                    $CS->template->setMeta('css', $css);

                // выставляем поля js
                if($options['js'] && is_string($options['js']))
                    $CS->template->setMeta('js', $options['js']);
                else if(is_array($options['js'])) foreach ($options['js'] as $css)
                    $CS->template->setMeta('js', $css);

                // обработаем страницу шаблоном и шаблонизатором
                $string = $CS->template->handleFile($filename);

                // создаем шаблон отображения
                $template_part = new CsTmpl(default_val($options['tmpl_part'], 'blank'), $CS->template);

                // ставим в шаблон отображения контент
                $template_part->set(default_val($options['content_buffer'], 'content'), $string);

                // добавляем шаблон отображения
                $CS->template->addTmpl($template_part);

                // установим шаблон отображения главным
                $CS->template->setMainTmpl($template_part);
            });
        });

        if(!empty($name))
        {
            $path = str_replace(CS__BASEPATH, '', $filename);
            $this->registeredPages[$name] = $path;
        }

        return TRUE;
    }

    /**
     * Получает массив опций из файла страницы
     * @param string $filename -- файл
     * @return array
     */
    private function getOptions(string $filename)
    {
        $CS = CubSystem::getInstance();

        // те опции, которые определены по дефолту,
        // но их можно переопределить, просто перезаписав
        $options_array = [];
        $options_array['title'] = '';
        $options_array['desc'] = '';
        $options_array['css'] = '';
        $options_array['js'] = '';
        $options_array['keywords'] = '';
        $options_array['template'] = FALSE;
        $options_array['tmpl_part'] = 'blank';
        $options_array['content_buffer'] = 'content';

        $moduleConfig = $this->config['module'];

        // опции отключены
        if(!$moduleConfig['enable_options'])
            return $options_array;

        // разрешен кеш, тогда пытаемся взять из кеша
        $options = ($moduleConfig['allow_cache_options'] !== FALSE) ?
            $CS->cache->get('options_' . $filename) : NULL;

        if(empty($options)) // кеш оказлся пустым
        {
            // читаем файл
            $data = file_get_contents($filename);

            // обрабатываем по паттерну:
            // /* OPTIONS_BLOCK {
            //
            //         ** здесь json **
            //
            // } */
            if(preg_match("!/\* OPTIONS_BLOCK(.*?)\*/!is", $data, $options))
            {
                // получаем json из выборки и преобразуем в массив
                $options = trim($options[1]);
                $options = json_decode($options, true);

                // если есть возможность записать в кеш, пишем
                if($moduleConfig['allow_cache_options'])
                {
                    $CS->cache->set('options_' . $filename, $options, $moduleConfig['cache_options_time']);
                }
            }
        }

        // если преобразование успешно, тогда пытаемся
        // перезаписать уже указанные опции юзерскими.
        if(is_array($options))
        {
            foreach ($options as $key=>$option)
            {
                if(array_key_exists($key, $options_array))
                {
                    $options_array[$key] = $option;
                }
            }
        }

        unset($data);

        return $options_array;
    }
}
