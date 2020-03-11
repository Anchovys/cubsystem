<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| fileoptions_helper.php, Назначение: хелпер управления опциями
| -------------------------------------------------------------------------
| В этом файле определен класс, в котором находятся методы
| для управления опциями в текстовых файлах.
|
@
@   Cubsystem CMS, (с) 2019
@   Author: Anchovy
@   GitHub: //github.com/Anchovys/cubsystem
@
*/

class fileoptions_helper {

    //путь до папки с опциями
    public $options_file = '';

    public function __construct ()
    {
        global $CS;
        //зададим папку с опциями
        $this->options_file = $CS->config['options_dir'];
    }

    /**
     * Функция сохранение массива.
     * Все данные в файле опций будут перезаписаны новыми.
     * @param $name - название опции.
     * @param $data - массив для сохранения. 
     * @return Bool
    */
    public function save ($name = '', $data = array()) 
    {
        //ничего не передано
        if(!$name || !is_array($data))
        {
            return false;
        }

        //зашифруем массив в json
        $json_data  = json_encode($data);

        //запишем файл
        return write_file($this->options_file . MD5Str($name), $json_data, 'wa+');
    }

    /**
     * Функция чтения опции. Вернет массив с данными.
     * Можно указать ключ, который нужно выбрать из массива.
     * @param $name - название опции.
     * @param $key - позволяет выбрать нужный элемент опции. если не указано - вернет весь массив.
     * @return Array & Boolean
    */
    public function read ($name = '', $key = '') 
    {
        //ничего не передано
        if(!$name)
        {
            return false;
        }

        //читаем файл (строка зашифрована в json)
        $json_data = read_file($this->options_file . MD5Str($name));

        //ошибка или пусто
        if(!$json_data) 
        {
            //вернем пустоту
            return false;
        }

        //получим расшифрованный массив
        $array_data = json_decode($json_data, true);

        if($key && array_key_exists($key, $array_data))
        {
            //есть ключ, вернем нужное
            $return = $array_data[$key];
        } else 
        {
            //нет ключа, передадим весь массив
            $return = $array_data;
        }

        //вернем результат
        return $return;
    }

    /**
     * Функция редактирования опции.
     * Перезаписывает данные с одинаковым ключом и дополняет новыми.
     * @param $name - название опции.
     * @param $data - массив с опциями. имеющиеся опции будут перезаписаны
     * @return Boolean
    */
    public function edit ($name = '', $data = array()) 
    {
        //проверим входные данные
        if(!$name || !is_array($data)) 
        {
            return false;
        }

        //прочитаем находящиеся данные в файле
        $array_data = $this->read($name);

        //проверим данные
        if(!is_array($array_data))
        {
            return false;
        }
        
        //комбинируем массив
        $data_combined = array_merge($array_data, $data);

        //сохраняем измененный файл
        return $this->save($name, $data_combined);
    }

    /**
     * Функция удаления файла опции или ключа опции.
     * Если не указать ключ, удалит файл целиком
     * @param $name - название опции.
     * @param $key - ключ опции для удаления. если не указан, удалится весь файл.
     * @param $byValue - удалить по значению, а не по ключу
     * @return Boolean
    */
    public function purge ($name = '', $key = '', $byValue = false)
    {
        //ничего не передано
        if(!$name)
        {
            return false;
        }

        if($key)//удаление по ключу
        {
            $array_data = $this->read($name);

            if(is_array($array_data))
            {

                if($byValue)
                {
                    if(in_array($key, $array_data))
                    {
                        if (($valKey = array_search($key, $array_data)) !== false)
                        {
                            unset($array_data[$valKey]);

                            //сохраняем измененный файл
                            return $this->save($name, $array_data);
                        }
                    }

                } else {

                    //проверяем ключ в массиве
                    if(array_key_exists($key, $array_data))
                    {
                        //меняем ключ
                        $array_data[$key] = '';

                        //сохраняем измененный файл
                        return $this->save($name, $array_data);
                    }

                }
            }

            return false;

        } else//удаление всего файла
        {
            //если файл есть
            if(file_exists($this->options_file . MD5Str($name)))
            {   
                //удалим
                return unlink($this->options_file . MD5Str($name));
            }
        }
    }
}



?>