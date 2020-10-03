<?php defined('CS__BASEPATH') OR exit('No direct script access allowed');
/* .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .
  .  @copyright Copyright (c) 2020, Anchovy.
  .  @author Anchovy, <contact.anchovy@gmail.com>
  .  @license MIT public license
  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  .  . */

/*
+ -------------------------------------------------------------------------
| errors.php [rev 1.0], Назначение: отлов PHP ошибок и их вывод
+ -------------------------------------------------------------------------
|
| Класс позволяет отлавливать ошибки и стандартизировать их вывод.
|
*/

class CsErrors
{
    // for singleton
    private static ?CsErrors $_instance = NULL;

    public static function getInstance() : CsErrors
    {
        if (self::$_instance == NULL)
            self::$_instance = new CsErrors();

        return self::$_instance;
    }

    public function init() : void
    {
        // for php errors
        set_error_handler([$this, 'handleError']);

        // for php exceptions
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError($level, $message, $file, $line, $context) : void
    {
        $this->printer(
            'Error type('.$level.'): msg:' . $message,
            CS_ENV === 'debug' ? 'cont:' . json_encode($context) : '',
            'src:' . $file . ' : ' . $line . ', gen_t: ' . time());
    }

    public function handleException($exception) : void
    {
        $this->printer(
            'Exception: code(' . $exception->getCode() . '): ' . $exception->getMessage(),
            'info:' . $this->jTraceEx($exception),
            'src:' . $exception->getFile() . ' : ' . $exception->getLine() . ', gen_t: ' . time());
    }

    private function jTraceEx($e, $seen=null) : string
    {
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
            $result  .= "\n" . $this->jTraceEx($prev, $seen);

        return $result;
    }

    private function printer($title = '', $body = '', $small = '') : void
    {
        print '<details style="background: white; font-family: \'PT Mono\', sans-serif;">';
            print '<summary><b>'. $title .'</b></summary>';
            print '<p>'. $body .'</p>';
            print '<p><small color="#ccc">'. $small .'</small></p>';
        print '</details>';
        die();
    }
}