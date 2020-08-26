<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

class SbArticle
{
    /*
     * CREATE TABLE `cubsystem`.`sb_articles` ( `id` INT NOT NULL AUTO_INCREMENT , `title` TEXT NOT NULL , `content_short` TEXT NOT NULL , `content_full` TEXT NOT NULL , `slug` TEXT NOT NULL , `author_id` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
     */

    public ?int $id;
    public ?string $title;
    public ?string $contentShort;
    public ?string $contentFull;
    public ?string $urlSlug;
    public ?int $authorId;
    public array $categories = [];
    public ?array $categoriesObjects = [];

    function __construct(array $data = NULL)
    {
        $CS = CubSystem::getInstance();

        if(empty($data)) return;

        // достаем данные из массива по ключам
        $id = default_val_array($data, 'id', 0);
        $title = default_val_array($data, 'title');
        $contentShort = default_val_array($data, 'content_short');
        $contentFull = default_val_array($data, 'content_full');
        $urlSlug = default_val_array($data, 'slug');
        $authorId = default_val_array($data, 'author_id');

        $this->id = CsSecurity::filter($id, 'int');
        $this->title = CsSecurity::filter($title);
        $this->contentShort = CsSecurity::filter($contentShort);
        $this->contentFull = CsSecurity::filter($contentFull);
        $this->urlSlug = CsSecurity::filter($urlSlug);
        $this->authorId = CsSecurity::filter($authorId, 'int');

        if(!empty($this->id))
        {
            // пытаемся получить обьект бд
            if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
                return NULL;

            $db->where('article_id', $this->id);
            $db->where('param_name', 'cat');

            $data = $db->get('sb_article_infos', null, 'value');

            if(!empty($data))
                foreach ($data as $cat)
                {
                    $this->categories[] = intval($cat['value']);
                }

            $this->categoriesObjects = SbCategory::getByIds($this->categories);
        }
    }

    public static function getCountArticles()
    {
        $CS = CubSystem::getInstance();
        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        $db->get('sb_articles');
        return $db->count;
    }

    /**
     * Выбрать одну SbArticle из БД по
     * property и selector
     * @param string $field - по какому столбцу выбирать, например, id
     * @param $value - значение столбца, например, 10.
     * @param array|null $needle - какие данные нужны от юзера
     * @return SbArticle|null
     * @throws Exception
     */
    private static function _getOneBy(string $field, $value, array $needle = NULL): ?SbArticle
    {
        $CS = CubSystem::getInstance();

        // фильтрация входных значений
        $field = CsSecurity::filter($field, 'base|string');
        $value = CsSecurity::filter($value, 'base');

        // после фильтрации, переменные могут быть empty,
        // проверим это
        if(empty_val($field, $value))
            return NULL;

        // разрешаем доступ только к этим полям
        if(!in_array($field, ['id', 'title', 'slug']))
            return NULL;

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        // очистка значений
        $field = $db->escape($field);
        $value = $db->escape($value);


        // выборка
        $db->where($field, $value);

        // строка со столбцами, которые надо получить
        $needleStr = empty($needle) ? '*' : implode(', ', $needle);

        if ($data = $db->getOne('sb_articles', $needleStr))
            return new SbArticle($data);

        return NULL;
    }

    /**
     * Выбрать несколько SbArticle из БД по
     * property и selector
     * @param string $field - по какому столбцу выбирать, например, id
     * @param $value - значение столбца, например, 10.
     * @param int $offset - смещение в количестве
     * @param int $count - количество записей для получения
     * @param array|null $needle - какие данные нужны от юзера
     * @param string $orderBy - сортировка по (указать поле)
     * @param bool $orderDesc - включить обратную сортировку
     * @return SbArticle|null
     * @throws Exception
     */
    private static function _getListBy(?string $field, $value, int $offset = 0, int $count = 10, array $needle = NULL, string $orderBy = 'id', bool $orderDesc = TRUE): ?Array
    {
        $CS = CubSystem::getInstance();

        // фильтрация входных значений
        $field = CsSecurity::filter($field, 'base|string');
        $value = CsSecurity::filter($value, 'base');

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        // очистка значений
        $field = $db->escape($field);
        $value = $db->escape($value);

        // выборка
        if(!empty_val($field, $value) && in_array($field, ['id', 'title', 'slug']))
            $db->where($field, $value);

        // сортировка
        $db->orderBy($orderBy, $orderDesc ? "DESC" : "ASC");

        // строка со столбцами, которые надо получить
        $needleStr = empty($needle) ? '*' : implode(', ', $needle);

        $data = $db->get('sb_articles', [$offset, $count], $needleStr);

        $result = [];
        if(!empty($data)) {
            foreach ($data as $item)
                $result[] = new SbArticle($item);
        }

        return [
                'count'      =>  count($result),
                'result'     =>  $result
        ];
    }

    public static function getOneById(int $id)
    {
        return self::_getOneBy('id', $id);
    }

    public static function getOneBySlug($slug)
    {
        return self::_getOneBy('slug', $slug);
    }

    public static function getLastPages(int $count = 10, int $page = 1, ?array $needle = NULL, $orderBy = 'id', $orderDesc = TRUE)
    {
        return self::_getListBy(NULL, NULL, $count * ($page - 1), $count, $needle, $orderBy, $orderDesc);
    }

    public static function getPagesInCategory(int $category, int $count = 10, int $page = 1, ?array $needle = NULL, $orderBy = 'id', $orderDesc = TRUE)
    {
        return self::_getListBy(NULL, NULL, $count * ($page - 1), $count, $needle, $orderBy, $orderDesc);
    }

    public function insert() : ?SbArticle
    {
        $CS = CubSystem::getInstance();

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;
        $data = [
                'title'         => $db->escape($this->title),
                'content_short' => $db->escape($this->contentShort),
                'content_full'  => $db->escape($this->contentFull),
                'slug'          => $db->escape($this->urlSlug),
                'author_id'     => $db->escape($this->authorId)
            ];

        // function returned current article id
        try { $id = $db->insert('sb_articles', $data); }
        catch (Exception $e) { return NULL; }

        if(isset($id) && is_int($id) && !empty($this->categories))
        {
            foreach ($this->categories as $category)
            {
                // TODO: Insert multi
                $db->insert('sb_article_infos', [
                    'article_id' => $id,
                    'param_name' => 'cat',
                    'value' => $category
                ]);
            }
        }

        // get article from database
        return is_int($id) ? self::getOneById($id) : NULL;
    }
}