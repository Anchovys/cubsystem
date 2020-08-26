<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

//CREATE TABLE `cubsystem`.`cs_sb_article_infos` ( `id` INT NOT NULL AUTO_INCREMENT , `article_id` INT NOT NULL , `param_name` TEXT NOT NULL , `value` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
class SbArticleInfo
{
    public ?int $id;
    public ?int $articleId;
    public ?string $name;
    public ?string $value;

    public function __construct(?array $data = null)
    {
        if(empty($data)) return;

        // достаем данные из массива по ключам
        $id = default_val_array($data, 'id', 0);
        $articleId = default_val_array($data, 'article_id');
        $name = default_val_array($data, 'param_name');
        $value = default_val_array($data, 'value');

        $this->id = CsSecurity::filter($id, 'int');
        $this->articleId = CsSecurity::filter($articleId, 'int');
        $this->name = CsSecurity::filter($name);
        $this->value = CsSecurity::filter($value);
    }

    # TODO: Для аргументов массивом?
    public static function getParamsForArticle(?int $article_id, ?string $paramName)
    {
        $CS = CubSystem::getInstance();

        // фильтрация входных значений
        $paramName = CsSecurity::filter($paramName, 'base|string');
        $id = intval($article_id);

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        if(!empty($article_id))
                $db->where('article_id', $id);
        if(!empty($paramName))
            $db->where('param_name', $paramName);

        // слишком много записей!!
        if(empty($article_id) && empty($paramName))
            return NULL;

        return $db->get('sb_article_infos');
    }

    public static function getParamsForValue(?string $paramName, $value)
    {
        $CS = CubSystem::getInstance();

        // фильтрация входных значений
        $paramName = CsSecurity::filter($paramName, 'base|string');
        $value = CsSecurity::filter($value, 'base|string');

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return NULL;

        if(!empty($value))
            $db->where('value', $value);
        if(!empty($paramName))
            $db->where('param_name', $paramName);

        // слишком много записей!!
        if(empty($value) && empty($paramName))
            return NULL;

        return $db->get('sb_article_infos');
    }

    public function insert() : bool
    {
        $CS = CubSystem::getInstance();

        // пытаемся получить обьект бд
        if ($CS->mysql === NULL || !$db = $CS->mysql->getObject())
            return FALSE;
        $data =
            [
                'article_id' => $this->articleId,
                'param_name' => $this->name,
                'value'      => $this->value
            ];

        try { $id = $db->insert('sb_article_infos', $data); }
        catch (Exception $e) { return FALSE; }

        return $id;
    }
}