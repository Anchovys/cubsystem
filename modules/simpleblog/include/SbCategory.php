<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */


// CREATE TABLE `cubsystem`.`cs_sb_categories` ( `id` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `description` TEXT NOT NULL , `slug` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB
class SbCategory
{
    public ?int $id;
    public ?string $name;
    public ?string $description;
    public ?string $urlSlug;

    public function __construct(array $data = null)
    {
        if(empty($data)) return;

        // достаем данные из массива по ключам
        $id = default_val_array($data, 'id', 0);
        $name = default_val_array($data, 'name');
        $description = default_val_array($data, 'description');
        $urlSlug = default_val_array($data, 'slug');

        $this->id = CsSecurity::filter($id, 'int');
        $this->name = CsSecurity::filter($name);
        $this->description = CsSecurity::filter($description);
        $this->urlSlug = CsSecurity::filter($urlSlug);
    }

    public static function getById(int $id) : ?SbCategory
    {
        $CS = CubSystem::getInstance();

        $id = intval($id);
        if(empty($id))
            return NULL;

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        $db->where('id', $id);
        $data = $db->getOne('sb_categories');
        return !empty($data) ? new SbCategory($data) : NULL;
    }

    public static function getByIds(array $ids)
    {
        $CS = CubSystem::getInstance();

        $ids = array_filter($ids, 'is_int');

        if(empty($ids))
            return NULL;

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        $db->where('id', $ids, 'IN');

        $objects = $db->get('sb_categories');
        return $objects;
    }

    public static function getAll($needle)
    {
        return self::_getListBy(NULL, NULL, NULL, NULL, $needle);
    }

    private static function _getListBy(?string $field, $value, int $offset = null, int $count = null, array $needle = null): ?Array
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
        if(!empty_val($field, $value) && in_array($field, ['id', 'article_id', 'name']))
            $db->where($field, $value);

        $offset_count = empty_val($offset, $count) ? NULL : [$offset, $count];
        $data = NULL;

        // строка со столбцами, которые надо получить
        $needleStr = empty($needle) ? '*' : implode(', ', $needle);

        try {
            $data = $db->get('sb_categories', $offset_count, $needleStr);
        } catch (Exception $e)
        {
            return NULL;
        }

        $result = [];
        if(!empty($data)) {
            foreach ($data as $item)
                $result[] = new SbCategory($item);
        }

        return [
            'count'      =>  count($result),
            'result'     =>  $result
        ];
    }

    public function update()
    {
        $CS = CubSystem::getInstance();

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        $this->name = $db->escape($this->name);

        if(empty($this->id))
            return NULL;

        $db->where('id', $this->id);

        if(!empty($this->name))
            $data['name'] = $db->escape($this->name);
        if(!empty($this->description))
            $data['description'] = $db->escape($this->description);
        if(!empty($this->urlSlug))
            $data['slug'] = $db->escape($this->urlSlug);

        // function returned current article id
        try { $id = $db->update('sb_categories', $data); }
        catch (Exception $e) { return NULL; }

        // get article from database
        return is_int($id) ? TRUE : NULL;
    }

    public function insert()
    {
        $CS = CubSystem::getInstance();

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;
        $data = [
            'name'          => $db->escape($this->name),
            'description'   => $db->escape($this->description),
            'slug'          => $db->escape($this->urlSlug)
        ];

        // function returned current article id
        try { $id = $db->insert('sb_categories', $data); }
        catch (Exception $e) { return NULL; }

        // get article from database
        return is_int($id) ? TRUE : NULL;
    }
}