<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class module_landing extends CsModule
{
    /**
     * Действия при загрузке модуля.
     * @return bool
     */
    public function onLoad()
    {
        $CS = Cubsystem::getInstance();
        $seekDir = $this->directory . 'pages' . _DS;
        $directories = CsFS::getDirectories($seekDir, FALSE);
        foreach ($directories as $directory)
        {
            if(!is_dir($seekDir . $directory))
                continue;

            $requestFile = $seekDir . $directory . _DS . 'index.php';
            if(!file_exists($requestFile))
                continue;

            $CS->router->get($directory, function() use(&$requestFile, &$CS)
            {
                $CS->info->setOption('landing_page', TRUE);
                $CS->info->setOption('landing_page_file', $requestFile);
                try {
                    $CS->hooks->register('system_print_tmpl', function () use(&$requestFile, &$CS)
                    {
                        $string = $CS->template->handleFile($requestFile);

                        $CS->template->mainId = 1;
                        $CS->template->getMainTmpl()->set('content', $string, 0);
                    });
                }
                catch (Error $e) {
                    $CS->errors->handleException($e);
                }
            });
        }

        return parent::onLoad();
    }
}