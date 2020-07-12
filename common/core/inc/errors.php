<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/**
 *
 *    CubSystem Minimal
 *      -> http://github.com/Anchovys/cubsystem/minimal
 *    copy, © 2020, Anchovy
 * /
 */

class CsErrors
{
    // for singleton
    private static ?CsErrors $_instance = NULL;

    /**
     * @return CsErrors
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsErrors();

        return self::$_instance;
    }

    public function init()
    {
        // for php errors
        set_error_handler([$this, 'handleError']);

        // for php exceptions
        set_exception_handler([$this, 'handleException']);

        // on stop script
        // for catch the fatal errors
        //register_shutdown_function([$this, 'handleStop']);
    }

    public function handleError($level, $message, $file, $line, $context)
    {
        $this->printer(
            'Error type('.$level.'): msg:' . $message,
            'cont:' . json_encode($context),
            'src:' . $file . ' : ' . $line . ', gen_t: ' . time());
        die();
    }

    public function handleException($exception)
    {
        $this->printer(
            'Exception: code(' . $exception->getCode() . '): ' . $exception->getMessage(),
            'info:' . $this->jTraceEx($exception),
            'src:' . $exception->getFile() . ' : ' . $exception->getLine() . ', gen_t: ' . time());
        die();
    }

    /*
    public function handleStop()
    {

    }
    */

    private function jTraceEx($e, $seen=null) {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen) $seen = array();
        $trace  = $e->getTrace();
        $prev   = $e->getPrevious();
        $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();
        while (true) {
            $current = "$file:$line";
            if (is_array($seen) && in_array($current, $seen)) {
                $result[] = sprintf(' ... %d more', count($trace)+1);
                break;
            }
            $result[] = sprintf(' at %s%s%s(%s%s%s)',
                count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line);
            if (is_array($seen))
                $seen[] = "$file:$line";
            if (!count($trace))
                break;
            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev)
            $result  .= "\n" . jTraceEx($prev, $seen);

        return $result;
    }

    private function printer($title = '', $body = '', $small = '')
    {
        print '<details style="background: white; font-family: \'PT Mono\', sans-serif;">';
            print '<summary><b>'. $title .'</b></summary>';
            print '<p>'. $body .'</p>';
            print '<p><small color="#ccc">'. $small .'</small></p>';
        print '</details>';
    }
}