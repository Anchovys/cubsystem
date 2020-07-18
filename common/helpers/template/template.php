<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class template_helper
{
    public    ?string $directory = NULL;
    public    ?array  $info = NULL;
    protected ?array  $_tmpl = NULL;

    private   array   $_meta = [];
    private   int     $mainId = 0;

    public function __construct()
    {
        if(!defined("CS_TEMPLATES_PATH"))
            define("CS_TEMPLATES_PATH", CS__BASEPATH . 'templates' . _DS);
        require_once (__DIR__ . _DS . 'tpml.php');
    }

    /**
     * @param string $name
     * @param string $dir
     * @return null|object
     */
    public function register(string $name, string $dir = '')
    {
        $CS = CubSystem::getInstance();

        $dir = default_val($dir, CS_TEMPLATES_PATH . $name . _DS);
        $dir = CsSecurity::filter($dir, 'path');

        if(!is_dir($dir) || !file_exists($dir . 'index.php'))
            return NULL;

        require_once ($dir . 'info.php');

        // конфигурация не задана
        if(!isset($info) || !is_array($info))
            return NULL;

        // проверим конфигурацию
        if($info['minv'] > $CS->info->getOption(['system', 'version']))
            return NULL;

        $this->info = $info;

        require_once ($dir . 'index.php');

        $full_name = $name . '_template';

        if(!class_exists($full_name))
            return NULL;

        $object = new $full_name();
        $object->directory = $dir;
        $object->onLoad();

        return $object;

    }

    /**
     * Добавляет новый шаблон в систему
     *
     * @param CsTmpl $tmpl - обьект шаблона
     * @param int $id - предпочитаемый ID
     * (можно не указывать, тогда будет добавлен следующим)
     * @param bool $setMain - установит этот шаблон главным.
     * обьязательно укажите id для этого
     */
    public function addTmpl(CsTmpl $tmpl, int $id = -1, bool $setMain = FALSE)
    {
        if($id < 0) $this->_tmpl[] = $tmpl;
        else {
            $this->_tmpl[$id] = $tmpl;
            if($setMain) $this->mainId = $id;
        }
    }

    /**
     * Вернет главный шаблон
     * @return CsTmpl
     */
    public function getMainTmpl()
    {
        return $this->_tmpl[$this->mainId];
    }

    /**
     * Вернет ID главного шаблона
     * @return int
     */
    public function getMainTmplId()
    {
        return $this->mainId;
    }

    /**
     * Поменять id главного шаблона
     * @param int $id - id, на который нужно поменять
     */
    public function setMainTmplId(int $id)
    {
        if(!array_key_exists($id, $this->_tmpl)) return;
        $this->mainId = $id;
    }

    public function setMainTmpl(CsTmpl $tmpl)
    {
        $tmpl_id = $this->getTmplId($tmpl);

        if(is_int($tmpl_id))
            $this->setMainTmplId($tmpl_id);
    }

    /**
     * @param int $id
     * @return CsTmpl
     */
    public function getTmpl(int $id = 0)
    {
        if(!array_key_exists($id, $this->_tmpl))
            return NULL;
        return $this->_tmpl[$id];
    }

    /**
     * Вызвращает ID переданного обьекта в массиве
     * Если не найден - вернет null
     * @param CsTmpl $tmpl - обьект шаблона
     * @return int|null
     */
    public function getTmplId(CsTmpl $tmpl)
    {
        if(!in_array($tmpl, $this->_tmpl))
            return NULL;

        $id = array_search($tmpl, $this->_tmpl);
        return default_val($id, NULL);
    }

    /**
     * @param string $name
     * @param $value
     * @param int $id
     * @return bool
     */
    public function setBuffer(string $name, $value, int $id = NULL) : bool
    {

        if($id === NULL)
            $id = $this->mainId;

        if(!key_exists($id, $this->_tmpl))
            return FALSE;

        $this->_tmpl[$id]->set($name, $value, TRUE);

        return TRUE;
    }

    /**
     * Добавляет данные в конец буфера
     * @param string $name
     * @param $value
     * @param int $id
     * @return bool
     */
    public function appendBuffer(string $name, string $value, int $id = NULL)
    {
        if($id === NULL)
            $id = $this->mainId;

        if(!key_exists($id, $this->_tmpl))
            return FALSE;

        $this->_tmpl[$id]->set($name, $value, TRUE, TRUE);

        return TRUE;
    }

    /**
     * @param int $id
     */
    public function showBuffer(int $id = 0) : void
    {
        // показываем главный буфер
        if($id === 0)
            $this->onDisplay();

        $result = $this->getBuffer($id);

        echo ($result !== FALSE) ?
            $result : 'Nothing to show!';
    }

    /**
     * @param int $id
     * @return bool|string
     */
    public function getBuffer(int $id = 0)
    {
        if(!key_exists($id, $this->_tmpl))
            return FALSE;

        $result = $this->_tmpl[$id]->out();

        return (count_chars($result) !== 0) ? $result : FALSE;
    }


    public function handleFile(string $file, $__data = '', $custom = null)
    {
        $CS = CubSystem::getInstance();
        if ( !file_exists($file))
            return FALSE;

        // извлечь массив
        if (is_array($__data))
            extract($__data);

        /* ***********   ***********   ***********   ***********   ***********
                кастомный случай, например для реализации шаблонизаторов,
                или других функций, обрабатывающих исходные коды шаблонов
        *  ***********   ***********   ***********   ***********   ********** */

        // получим код из файла
        $code = file_get_contents($file);
        $code = '?>' . $code . '<?php';

        // обработаем шаблонизатором
        $code = $this->tmpl_prepare($code);

        // если в custom, например, функция
        if (is_callable($custom)) {
            // вызов функции
            $res = $custom($code);

            // вернула string, заменим целиком
            if (is_string($res))
                $code = $res;
        }

        ob_start();

        try {   // попытка выполнить php код
            eval($code);
        } catch (ParseError $e) {
            $CS->errors->handleException($e);
        }

        return ob_get_clean();
    }

    public function onLoad()
    {
        return TRUE;
    }

    public function onDisplay()
    {
        return TRUE;
    }

    private function tmpl_prepare($template)
    {
        $template =  str_replace(
            array('{?', '?}', '{{', '}}', '{%', '%}'),
            array('<?=', '??""?>', '<?=', '?>', '<?php', '?>'),
            $template);

        return $template;
    }

    public function autoloadAssets($directory, string $ext = 'css', string $assetsDir = '')
    {
        $finally_html = "";
        $assetsDir = default_val($assetsDir, $this->directory . 'assets' . _DS);
        $full_directory = CsSecurity::filter($assetsDir . $directory . _DS, 'path');

        if(!CsFS::dirExists($full_directory))
            return '';

        $files = CsFS::getFiles($full_directory, 0, $ext);

        foreach ($files as $file)
        {
            $url = CsUrl::pathToUrl($file);

            switch ($ext)
            {
                case 'css':
                    $finally_html .= "<link rel=\"stylesheet\" href=\"{$url}\">";
                    break;
                case 'js':
                    $finally_html .= "<script src=\"{$url}\"></script>";
                    break;
            }
        }

        return $finally_html;
    }

    /**
     * Поставить Meta данные
     * @param $key
     * @param $value
     */
    public function setMeta(string $key, $value): void
    {
        $this->_meta[] =
            [
                'k' => $key,
                'v' => $value
            ];
    }

    /**
     * Получить HTML код всех meta
     * @return string|null
     */
    public function getTotalMeta(): ?string
    {
        $total_data = NULL;
        if(is_array($this->_meta))
            foreach ($this->_meta as $value)
            { $key = $value['k']; $value = $value['v'];
                switch ($key)
                {
                    case 'title':
                        $total_data .= "<title>{$value}</title>";
                        $total_data .= "<meta property=\"og:title\" content=\"{$value}\">";
                        break;

                    case 'description':
                        $total_data .= "<meta name=\"description\" content=\"{$value}\">";
                        $total_data .= "<meta property=\"og:description\" content=\"{$value}\">";
                        break;

                    case 'keywords':
                        $total_data .= "<meta name=\"keywords\" content=\"{$value}\">";
                        $total_data .= "<meta property=\"og:keywords\" content=\"{$value}\">";
                        break;

                    case 'icon':
                    case 'favicon':
                        $path = $this->directory . 'assets' . _DS . 'img' . _DS . 'favicons' . _DS;
                        $url = CsUrl::pathToUrl($path);
                        $total_data .= "<link rel=\"icon shortcut\" href=\"{$url}{$value}\" type=\"image/x-icon\">";
                        break;

                    case 'css':
                    case 'stylesheet':
                        $path = $this->directory . 'assets' . _DS . 'css' . _DS;
                        $url = CsUrl::pathToUrl($path);
                        $total_data .= "<link rel=\"stylesheet\" href=\"{$url}{$value}\">";
                        break;

                    case 'js':
                    case 'script':
                        $path = $this->directory . 'assets' . _DS . 'js' . _DS;
                        $url = CsUrl::pathToUrl($path);
                        $total_data .= "<script src=\"{$url}{$value}\"></script>";
                        break;

                    case 'meta':
                        if(key_exists('property', $value))
                            $total_data .= "<meta property=\"{$value['property']}\" content=\"{$value['content']}\">";
                        else if (key_exists('name', $value))
                            $total_data .= "<meta name=\"{$value['name']}\" content=\"{$value['content']}\">";
                        break;
                }
            }
        return $total_data;
    }
}