<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
+ -------------------------------------------------------------------------
| filesystem.php [rev 1.0], Назначение: набор функций файловой системы
+ -------------------------------------------------------------------------
|
| Класс позволяет удобно взаимодействовать с файловой системой.
|
*/

class CsFS
{
    /**
     * Вернет массив директорий в указанной директории
     *
     * @param string $source_dir - директория, в которой искать другие директории
     * @param int $depth - глубина поиска
     * @return array
     */
    public static function getDirectories($source_dir, int $depth = 0) : array
    {
        $depth = $depth+2;
        if($depth <= 0) return [];

        $map = self::directoryMap($source_dir, $depth, TRUE, FALSE, FALSE);

        return array_keys_recursive($map);
    }

    /**
     * Вернет массив файлов в данной директории
     *
     * @param string $source_dir - директория, в которой искать другие директории
     * @param int $depth - глубина поиска
     * @param array|string $ext - расширения или массив расширений для поиска
     * @return array
     */
    public static function getFiles(string $source_dir, int $depth = 0, $ext = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg']) : array
    {
        $depth = $depth+2;
        if($depth <= 0) return [];

        $map = self::directoryMap($source_dir, $depth, TRUE);
        $files = array_values_recursive($map);

        $return = [];
        foreach ($files as $file)
        {
            $extension = self::getExt($file);
            if(is_array($ext) && array_key_exists($extension, $ext))
            {
                $return[] = $file;
            } else if(is_string($ext) && $extension === $ext)
            {
                $return[] = $file;
            }
        }

        return $return;
    }

    /**
     * Просканировать директорию. Вернет массив вида
     *
     * [
     *      'dir name' =>
     *      [
     *          '0' => 'file.php'
     *          'sub_dir' =>
     *          [
     *              '0' => 'file.php'
     *          ]
     *      ]
     * ]
     *
     * @param string $source_dir - директория, в которой искать другие директории
     * @param int $directory_depth - макс. вложенность директорий
     * @param bool $full_path - в качестве результата вернуть полный путь?
     * @param bool $hidden - обрабатывать скрытые директории (., ..)
     * @param bool $files
     * @return array|bool
     */
    public static function directoryMap(string $source_dir, int $directory_depth = 0, bool $full_path = TRUE, bool $hidden = FALSE, bool $files = TRUE)
    {
        if ($fp = @opendir($source_dir))
        {
            $return	= [];
            $new_depth	= $directory_depth - 1;
            $source_dir	= rtrim($source_dir, _DS)._DS;

            while (($file = readdir($fp)) !== FALSE)
            {
                $new_source = $source_dir . $file . _DS;
                $filename = $full_path ? $source_dir . $file : $file;

                // Remove '.', '..', and hidden files [optional]
                if (!trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) continue;

                if (($directory_depth < 1 OR $new_depth > 0) && self::dirExists($new_source))
                    $return[$filename] = self::directoryMap($new_source, $new_depth, $full_path, $hidden, $files);
                else if($files) $return[] = $filename;
            }

            closedir($fp);
            return $return;
        }

        return FALSE;
    }

    /**
     * Попытаться удалить расширение файла
     *
     * @param string $file - имя файла
     * @return string|string[]
     */
    public static function removeExt(string $file) : string
    {
        return str_replace('.' . self::getExt($file), '', $file);
    }

    /**
     * Получает расширение файла
     * Например для image.png, выведет png
     *
     * @param string $file - имя файла
     * @return string
     */
    public static function getExt(string $file) : string
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    public static function fileExists(string $fileName) : bool
    {
        return file_exists($fileName) && is_file($fileName);
    }

    /**
     * Выводит true / false в зависимости от того,
     * существует ли указанная папка
     *
     * @param string $dirName - имя директории
     * @return bool
     */
    public static function dirExists(string $dirName) : bool
    {
        return file_exists($dirName) && is_dir($dirName);
    }

    /**
     * Создает директорию если ее нет
     *
     * @param string $task - имя директории
     * @param int $mode - права, с которыми нужно создать директорию
     * @param bool $recursive - флаг для рекурсивного создания
     * @return bool
     */
    public static function mkdirIfNotExists($task, int $mode = 777, bool $recursive = true) : bool
    {
        if(is_array($task))
        {
            foreach ($task as $dir)
            {
                if(!CsFS::dirExists($dir))
                {
                    mkdir($dir, $mode, $recursive);
                }
            }

            return TRUE;
        }

        if(CsFS::dirExists($task))
            return TRUE;

        return mkdir($task, $mode, $recursive);
    }


    static function folderSize(string $dir) : int
    {
        if(!self::dirExists($dir))
          return 0;

        $size = 0;

        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : folderSize($each);
        }
        return $size;
    }
}
