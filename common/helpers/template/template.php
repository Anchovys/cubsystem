<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

define("CS_TEMPLATES_PATH", CS__BASEPATH . 'templates' . _DS);
require_once (__DIR__ . _DS . 'tpml.php');

class template_helper
{
    public    ?string $directory = NULL;
    public    ?array  $info = NULL;
    protected ?array  $_tmpl = NULL;

    private   array   $_meta;
    public    int     $mainId = 0;

    /**
     * @param string $name
     * @param null $dir
     * @return null|object
     */
    public function register(string $name, $dir = NULL)
    {
        $CS = CubSystem::getInstance();

        $dir = ($dir !== NULL) ? $dir :
            CS_TEMPLATES_PATH . $name . _DS;


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

    public function onLoad()
    {
        return TRUE;
    }

    public function onDisplay()
    {
        return TRUE;
    }

    /**
     * @param CsTmpl $tmpl
     * @param int $id
     */
    public function addTmpl(CsTmpl $tmpl, int $id = -1)
    {
        if($id === -1)
            $this->_tmpl[] = $tmpl;
        else $this->_tmpl[$id] = $tmpl;
    }

    /**
     * @return CsTmpl
     */
    public function getMainTmpl()
    {
        return $this->_tmpl[$this->mainId];
    }

    /**
     * @param int $id
     * @return CsTmpl
     */
    public function getTmpl(int $id = 0)
    {
        return $this->_tmpl[$id];
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

    function tmpl_prepare($template)
    {
        $template =  str_replace(
            array('{?', '?}', '{{', '}}', '{%', '%}'),
            array('<?=', '??""?>', '<?=', '?>', '<?php', '?>'),
            $template);

        return $template;
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