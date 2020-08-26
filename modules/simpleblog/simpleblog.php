<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class module_simpleblog extends CsModule
{
    /**
     * Действия при загрузке модуля.
     * @return bool
     */
    public function onLoad()
    {
        $CS = CubSystem::getInstance();

        /* Подключим все нужные классы */
        require_once($this->directory . 'include' . _DS . 'SbPagination.php');
        require_once($this->directory . 'include' . _DS . 'SbArticleInfo.php');
        require_once($this->directory . 'include' . _DS . 'SbArticle.php');
        require_once($this->directory . 'include' . _DS . 'SbCategory.php');
        require_once($this->directory . 'include' . _DS . 'SbController.php');

        /*============
            Инициализация обработчиков AJAX
            Добавляем все обработчики AJAX.
        */

        if($CS->ajax !== NULL)
        {
            /*============
                ДОБАВЛЕНИЕ/ОБНОВЛЕНИЕ КАТЕГОРИИ
                обработчик добавления новой категории
            */
            $CS->ajax->handle('sb_cat', function () use ($CS) {
                if($post = CsSecurity::checkPost(['token', 'name', 'description'])) {
                    // проверяем правильность токена
                    if(CsSecurity::checkCSRFToken($post['token']) === FALSE)
                        return;

                    // проверки
                    $name = CsSecurity::filter($post['name']);
                    $description = CsSecurity::filter($post['description']);
                    $slug = !empty($post['slug']) ? $post['slug'] :
                        CsSecurity::filter($name, 'transliterate');
                    $slug = CsSecurity::filter($slug, 'spaces;special_string;to_lower');

                    // TODO: Проверка по slug перед добавлением

                    sleep(3);

                    if(!empty($id = intval($post['id'])))
                    {
                        $category = SbCategory::getById($id);
                        $category->name = $name;
                        $category->description = $description;
                        $category->urlSlug = $slug;

                        $category->update();
                    }
                    else {

                        $category = new SbCategory();
                        $category->name = $name;
                        $category->description = $description;
                        $category->urlSlug = $slug;

                        $category->insert();
                    }

                    CsUrl::redir($_SERVER['HTTP_REFERER'], false);
                }
            }, function () use ($CS) {
                return $CS->admin->hasAccess();
            });

            /*============
                ДОБАВЛЕНИЕ СТРАНИЦЫ
                обработчик добавления новой страницы
            */
            $CS->ajax->handle('sb_page', function () use ($CS) {
                if($post = CsSecurity::checkPost(['token', 'title', 'content']))
                {
                    // проверяем правильность токена
                    if(CsSecurity::checkCSRFToken($post['token']) === FALSE)
                        return;

                    // проверки
                    $title = CsSecurity::filter($post['title']);
                    $content = CsSecurity::filter($post['content']);
                    //$categories = array_filter($post['categories'], 'is_int');
                    $slug = CsSecurity::filter($title, 'transliterate;spaces;special_string;to_lower');
                    $user_id = $CS->auth->getCurrent()->id;

                    // после проверки значения могут быть пустыми
                    if(empty_val($title, $content, /*$categories,*/ $slug, $user_id))
                        return;

                    // перед добавлением - спим 3 секунды
                    sleep(3);

                    // не должно быть записей с таким-же slug
                    if(SbArticle::getOneBySlug($slug) !== NULL)
                        return;

                    // формируем обьект
                    $article = new SbArticle();
                    $article->title = $title;
                    $article->contentFull = $content;
                    $article->contentShort = $content;
                    $article->categories = [1,2,3,4];
                    $article->urlSlug = $slug;
                    $article->authorId = $user_id;

                    // заносим в бд
                    $article->insert();

                    CsUrl::redir($_SERVER['HTTP_REFERER'], false);
                }

            }, function () use ($CS) {
                return $CS->admin->hasAccess();
            });
        }

        /*============
            Инициализация в админ-панели
            Регистрируем модуль для админ-панели
        */

        if($CS->admin !== NULL)
        {
            $CS->admin->menu->add('Simple Blog', 's-blog');
            $CS->admin->setAction('s-blog', function () use ($CS) {
                $inc_admin_path = $this->directory . 'include' . _DS . 'admin' . _DS;
                $data = [
                    'token' => $CS->info->getOption('security_CSRF-secure_token'),
                ];
                switch (CsUrl::segment(2))
                {
                    case "article":
                        $buffer = $CS->template->handleFile( $inc_admin_path . 'editor.php', $data);
                        $CS->template->getMainTmpl()->set('content', $buffer);
                        break;
                    case "category":

                        if(isset($_GET['id']) && !empty($id = intval($_GET['id'])))
                        {
                            $cat = SbCategory::getById($id);

                            if(!empty($cat))
                            {
                                $data['id'] = $id;
                                $data['cat_name'] = $cat->name;
                                $data['cat_slug'] = $cat->urlSlug;
                                $data['cat_description'] = $cat->description;
                            }
                        }

                        $buffer = $CS->template->handleFile( $inc_admin_path . 'addcategory.php', $data);
                        $CS->template->getMainTmpl()->set('content', $buffer);

                        break;
                    default:
                    case FALSE:

                        $data['articles_list'] = "";
                        foreach (SbArticle::getLastPages(25,
                            default_val_array($_GET, 'page', 1))['result'] as $item) {
                            $tmpl = new CsTmpl('blocks/basic/card', $CS->template);
                            $tmpl->set('card_title', '<a href='.CsUrl::absUrl('admin/s-blog/article?id=' . $item->id).'>' .
                                $item->title . '</a> (' . $item->urlSlug . ')');
                            $tmpl->set('card_text', $item->contentShort);
                            $data['articles_list'] .= $tmpl->out();
                        }

                        $data['categories_list'] = "";
                        foreach (SbCategory::getAll(NULL)['result'] as $item) {
                            $tmpl = new CsTmpl('blocks/basic/card', $CS->template);
                            $tmpl->set('card_title', '<a href='.CsUrl::absUrl('admin/s-blog/category?id=' . $item->id).'>' .
                                $item->name . '</a> (' . $item->urlSlug . ')');

                            $tmpl->set('card_text', $item->description);
                            $data['categories_list'] .= $tmpl->out();
                        }

                        $buffer = $CS->template->handleFile($inc_admin_path . 'index.php', $data);
                        $CS->template->getMainTmpl()->set('content', $buffer);
                        break;
                }
            });
        }

        /* Обьект контроллера, который используется для адресации */
        $controller = new SbController();

        /*============
            Маршрутизация
            Здесь мы регистрируем все маршруты на контроллер
        */

        // регистрация корневого slug
        if($this->config['register_root_as_home_slug'])
            $CS->router->get('/', [$controller, 'Home']);

        // домашний slug
        $home_slug = default_val_array($this->config, 'home_slug', 'home');
        $CS->router->get('/' . $home_slug, [$controller, 'Home']);

        // все остальное
        $CS->router->get('/category', [$controller, 'Category']);
        $CS->router->get('/category/{cat}', [$controller, 'Category']);
        $CS->router->get('/article/{slug}', [$controller, 'Article']);
        $CS->router->get('/sitemap', [$controller, 'Sitemap']);


        return parent::onLoad();
    }

    public function onEnable()
    {

        return parent::onEnable();
    }

    public function onDisable()
    {

        return parent::onDisable();
    }

    public function onPurge()
    {

        return parent::onPurge();
    }
}