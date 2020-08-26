<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class SbController
{
    /**
     * Функция контроллера для работы с отдельной страницей
     * @param string $slug - slug на запись
     */
    public function Article($slug = '')
    {
        $CS = CubSystem::getInstance();
        $template = $CS->template;

        // получаем страницу по Slug
        $article = SbArticle::getOneBySlug($slug);

        // обрабатываем 404 - если страница не найдена
        if($article == null)
        {
            $this->NotFoundPage();
            return;
        }

        // вывод данных на шаблон страницы
        $articleTmpl = new CsTmpl('simpleblog/full_article', $template);
        $articleTmpl->set('title', $article->title);
        $articleTmpl->set('text', $article->contentFull);

        if(!empty($article->categoriesObjects))
        {
            $catLinks = [];
            foreach($article->categoriesObjects as $cat)
                $catLinks[]= '<a class="badge badge-primary" href="'.CsUrl::absUrl('category/' . $cat['id']).'">'. $cat['name'] .'</a>';

            $articleTmpl->set('catLinks', implode(' ', $catLinks));
        } else $articleTmpl->set('catLinks', 'без категории...');

        // --- вывод на шаблон --- //
        $blogTmpl = new CsTmpl('simpleblog/home', $template);
        $blogTmpl->set('articles', $articleTmpl->out());
        $blogTmpl->set('pagination', '');

        $blankTmpl = $template->getTmpl(1);
        $blankTmpl->set('content', $blogTmpl->out());
        $template->setMainTmpl($blankTmpl);
    }
    /**
     * Функция контроллера для работы со страницей категорий
     * @param string $cat - id категории, которую показывать
     */
    public function Category($cat = '')
    {
        $CS = CubSystem::getInstance();
        $template = $CS->template;

        $buffer = '';
        if(!empty($cat = intval($cat)))
        {
            $paginate = new SbPagination();
            $paginate->setCurrentPage(default_val_array($_GET, 'page', 1));
            $paginate->setTotal(SbArticle::getCountArticles());
            $paginate->setLimit(20);

            $articles = SbArticle::getPagesInCategory($cat, $paginate->getLimit(), $paginate->getCurrentPage(), ['slug', 'content_short', 'title']);
            if ($articles['count'] !== 0) {
                foreach ($articles['result'] as $article) {
                    // вывод данных на шаблон страницы
                    $articleTmpl = new CsTmpl('simpleblog/short_article', $template);
                    $articleTmpl->set('title', $article->title);
                    $articleTmpl->set('text', $article->contentShort);
                    $articleTmpl->set('slug', $article->urlSlug);

                    $catLinks = '';
                    foreach ($article->categoriesObjects as $cat)
                        $catLinks .= '<a href="' . CsUrl::absUrl('category/' . $cat['id']) . '">' . $cat['name'] . '</a>';

                    $articleTmpl->set('catLinks', $catLinks);
                    $buffer .= $articleTmpl->out();
                }
            } else return $this->NotFoundPage();

            // --- вывод на шаблон --- //
            $blogTmpl = new CsTmpl('simpleblog/home', $template);
            $blogTmpl->set('articles', $buffer);
            $blogTmpl->set('pagination', $paginate->getHtml());
            $buffer = $blogTmpl->out();
        } else
        {

        }

        $blankTmpl = $template->getTmpl(1);
        $blankTmpl->set('content', $buffer);
        $template->setMainTmpl($blankTmpl);
    }

    /**
     * Функция контроллера для работы карты сайта (Sitemap)
     */
    public function Sitemap()
    {
    }

    /**
     * Функция контроллера для работы главной страницы
     */
    public function Home()
    {
        $CS = CubSystem::getInstance();
        $template = $CS->template;

        $paginate = new SbPagination();
        $paginate->setCurrentPage(default_val_array($_GET, 'page', 1));
        $paginate->setTotal(SbArticle::getCountArticles());
        $paginate->setLimit(20);

        $articles = SbArticle::getLastPages($paginate->getLimit(), $paginate->getCurrentPage(), ['slug', 'content_short', 'title']);
        $buffer = '';
        if ($articles['count'] !== 0) {
            foreach ($articles['result'] as $article) {
                // вывод данных на шаблон страницы
                $articleTmpl = new CsTmpl('simpleblog/short_article', $template);
                $articleTmpl->set('title', $article->title);
                $articleTmpl->set('text', $article->contentShort);
                $articleTmpl->set('slug', $article->urlSlug);

                $catLinks = '';
                foreach ($article->categoriesObjects as $cat)
                    $catLinks .= '<a href="' . CsUrl::absUrl('category/' . $cat['id']) . '">' . $cat['name'] . '</a>';

                $articleTmpl->set('catLinks', $catLinks);
                $buffer .= $articleTmpl->out();
            }
        } else return $this->NotFoundPage();

        // --- вывод на шаблон --- //
        $blogTmpl = new CsTmpl('simpleblog/home', $template);
        $blogTmpl->set('articles', $buffer);
        $blogTmpl->set('pagination', $paginate->getHtml());

        $blankTmpl = $template->getTmpl(1);
        $blankTmpl->set('content', $blogTmpl->out());
        $template->setMainTmpl($blankTmpl);
    }

    /**
     * Функция при ошибке 404 (запись не найдена)
     */
    public function NotFoundPage()
    {
        $CS = CubSystem::getInstance();
        $template = $CS->template;

        // вывод данных об ошибке 404
        $articleTmpl = new CsTmpl('blocks/basic/card', $template);
        $articleTmpl->set('card_title', '404 - несуществующая страница');
        $articleTmpl->set('card_text', 'Извините, по вашему запросу ничего не найдено!');

        // --- вывод на шаблон --- //
        $blankTmpl = $template->getTmpl(1);
        $blankTmpl->set('content', $articleTmpl->out());
        $template->setMainTmpl($blankTmpl);
    }
}