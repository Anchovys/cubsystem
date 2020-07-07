<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class default_template extends template_helper
{
    public ?CsTmpl $indexTmpl = NULL;
    public ?CsTmpl $mainTmpl = NULL;

    /**
     * Действия при загрузке шаблона
     *
     * @return bool
     */
    public function onLoad()
    {
        // входной шаблон (   parts/index.php   )
        // содержит два буфера - body, head
        $this->indexTmpl = new CsTmpl('index', $this);

        // главный шаблон (   parts/index.php   )
        // содержит юзерские буферы
        $this->mainTmpl  = new CsTmpl('main', $this);

        // зарегистрируем два шаблона на Id 0 и 1
        $this->addTmpl($this->indexTmpl, 0);
        $this->addTmpl($this->mainTmpl,  1);

        // поставим main id = 1
        $this->mainId = 1;

        // вернем True
        return parent::onLoad();
    }

    /**
     * Действия при отображении шаблона
     * Вызывается при вызове главного шаблона
     * @return bool
     */
    public function onDisplay()
    {
        // можно поставить свои Meta данные
        $this->setMeta('title', 'Hello world!');

        // во входной шаблон добавляем буферы
        // в body добавим то, что вернул пользовательский
        $this->indexTmpl
            ->set('head', $this->getTotalMeta())
            ->set('body', $this->mainTmpl->out());

        // вернем True
        return parent::onDisplay();
    }
}