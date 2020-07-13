<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class default_template extends template_helper
{
    public ?CsTmpl $indexTmpl = NULL;
    public ?CsTmpl $mainTmpl = NULL;
    public ?CsTmpl $blankTmpl = NULL;

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

        // "пустой", для ввода в входной буфера - body
        $this->blankTmpl = new CsTmpl('blank', $this);

        // зарегистрируем два шаблона на Id 0 , 1 и 2
        $this->addTmpl($this->indexTmpl, 0);
        $this->addTmpl($this->blankTmpl,  1);
        $this->addTmpl($this->mainTmpl,  2);

        // поставим main id = 2
        $this->mainId = 2;

        // вернем True
        return parent::onLoad();
    }

    /**
     * Действия при отображении шаблона
     * Вызывается при вызове корневого шаблона
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
            ->set('body', $this->getMainTmpl()->out());

        // вернем True
        return parent::onDisplay();
    }
}