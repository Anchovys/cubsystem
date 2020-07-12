<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class CsTmpl
{
    public string  $directory;
    public ?string $templateFile;
    private array $_buffer = [];
    private ?template_helper $template;

    public function __construct(string $part = 'index', $template = NULL)
    {
        $CS = CubSystem::getInstance();

        $this->template = $template === null ? $CS->template : $template;
        $this->directory = $template->directory;

        $this->templateFile  = $template->directory;
        $this->templateFile .= 'parts' . _DS . $part . '.php';

        $this->_buffer = ['CS'=>$CS];

        if(!file_exists($this->templateFile) || !is_readable($this->templateFile))
            return NULL;

        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @param bool $override
     * @param bool $append
     * @return CsTmpl|null
     */
    public function set(string $name, $value, bool $override = TRUE, bool $append = FALSE)
    {
        if(key_exists($name, $this->_buffer) && !$override)
            return NULL;

        if($append) $this->_buffer[$name] .= $value;
        else $this->_buffer[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return array|bool
     */
    public function get(string $name)
    {
        if(key_exists($name, $this->_buffer))
            return $this->_buffer;
        return FALSE;
    }

    /**
     * @param bool $echo
     * @return bool|false|string
     */
    public function out(bool $echo = FALSE)
    {

        $out = $this->outf($this->templateFile, $this->_buffer);
        if($echo) echo $out;
        return $out;
    }

    /**
     * @param string $file
     * @param string $__data
     * @return bool|false|string
     */
    private function outf(string $file, $__data = '')
    {
        return $this->template->handleFile($file, $__data);
    }

    public function unset()
    {
        unset($this->_buffer);
    }
}