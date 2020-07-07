<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class CsFS
{
    /**
     * Вернет массив директорий в указанной директории
     *
     * @param string $source_dir    - директория, в которой искать другие директории
     * @param bool $full_path       - в качестве результата вернуть полный путь?
     * @return array
     */
    public static function getDirectories(string $source_dir, bool $full_path = TRUE)
    {
        $filedata = array();

        $dirs = self::directoryMap($source_dir, 0, $full_path, FALSE);
        foreach ($dirs as $dir=>$files)
        {
            if(is_string($dir))
                $filedata[] = $dir;
        }
        return $filedata;
    }

    /**
     * Вернет массив файлов в данной директории
     *
     * @param string $source_dir - директория, в которой искать другие директории
     * @param bool $full_path
     * @param array $exts
     * @return array
     */
    public static function getFiles(string $source_dir, bool $full_path = TRUE, array $exts = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg'])
    {
        $filedata = array();
        $files = self::directoryMap($source_dir, 0, TRUE, FALSE);

        foreach ($files as $key=>$item)
        {
            if(is_string($item) && is_file($item) && in_array(self::getExt($item), $exts))
                $filedata[] = $full_path ? $item : str_replace($source_dir, '', $item);
        }
        return $filedata;
    }

    /**
     * Просканировать директорию. Вернет массив вида
     *
     * [
     *      'dir name' =>
     *      [
     *          '0' => 'file.php'
     *      ]
     * ]
     *
     * @param string $source_dir    - директория, в которой искать другие директории
     * @param int $directory_depth  - макс. вложенность директорий
     * @param bool $full_path       - в качестве результата вернуть полный путь?
     * @param bool $hidden          - обрабатывать скрытые директории (., ..)
     * @return array|bool
     */
    public static function directoryMap(string $source_dir, int $directory_depth = 0, bool $full_path = TRUE, bool $hidden = FALSE)
    {
        if ($fp = @opendir($source_dir))
        {
            $filedata	= array();
            $new_depth	= $directory_depth - 1;
            $source_dir	= rtrim($source_dir, _DS)._DS;

            while (FALSE !== ($file = readdir($fp)))
            {
                // Remove '.', '..', and hidden files [optional]
                if (!trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) continue;

                if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir . $file))
                    $filedata[$file] = self::directoryMap($source_dir . $file . _DS, $new_depth, $full_path, $hidden);
                else
                    $filedata[] = $full_path ? $source_dir . $file : $file;
                    // $filedata[] = htmlentities($file, ENT_QUOTES, 'cp1251');
            }

            closedir($fp);
            return $filedata;
        }

        return FALSE;
    }

    /**
     * Попытаться удалить расширение файла
     *
     * @param string $file - имя файла
     * @return string|string[]
     */
    public static function removeExt(string $file)
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
    public static function getExt(string $file)
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    public static function fileExists(string $fileName)
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
    public static function dirExists(string $dirName)
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
    public static function mkdirIfNotExists($task, int $mode = 777, bool $recursive = true)
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
}