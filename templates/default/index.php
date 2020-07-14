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
        // можно поставить свои Meta данные
        $this->setMeta('title', 'Hello world!');

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
        $this->addTmpl($this->blankTmpl, 1);
        $this->addTmpl($this->mainTmpl,  2);

        // ставим главный
        $this->setMainTmpl($this->mainTmpl);

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

        $autoload = $this->autoloadAssets('css/autoload', 'css') .
                    $this->autoloadAssets('js/autoload', 'js');

        $lazy = $this->autoloadAssets('css/lazy', 'css') .
                $this->autoloadAssets('js/lazy', 'js');

        // во входной шаблон добавляем буферы
        // в body добавим то, что вернул пользовательский
        $this->indexTmpl
            ->set('head', $this->getTotalMeta() . $autoload)
            ->set('body', $this->getMainTmpl()->out() . $lazy);

        // вернем True
        return parent::onDisplay();
    }
}