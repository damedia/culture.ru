<?php

define('LOG_FILE', dirname(__FILE__) . '/logs/_debug.log');
define('LOG_FILE_SOAP_PACKETS', dirname(__FILE__) . '/logs/soap.log');

$DBG['STOP'] = false;

class gFuncs
{
    public static function readDirectory($directory, $recursive = false)
    {
        $array_items = array();
        if ($handle = opendir($directory))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != "..")
                {
                    if (is_dir($directory . "/" . $file))
                    {
                        if ($recursive)
                        {
                            $array_items = array_merge($array_items, directoryToArray($directory . "/" . $file, $recursive));
                        }
                        $file = $directory . "/" . $file;
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    }
                    else
                    {
                        $file = $directory . "/" . $file;
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    public static function dbgWriteLog($mes, $logFile = LOG_FILE)
    {
        $fd = fopen($logFile, "a");
        fwrite($fd, "[" . date("Y-m-d H:i:s") . "] " . $mes . "\r\n");
        fclose($fd);
    }

    public static function clearLog()
    {
        $fd = fopen(LOG_FILE, "w");
        fclose($fd);
    }

    public static function dbgWriteLogVar($var, $clearLog = false, $prefix = "", $logFile = LOG_FILE)
    {
        if ($clearLog)
        {
            self::clearLog();
        }

        ob_start();
        print_r($var);
        $mes = ob_get_contents();
        ob_end_clean();

        self::dbgWriteLog($prefix . "\r\n" . $mes, $logFile);
    }

    public static function dbgWriteLogDoctrine($var, $level, $clearLog = false, $prefix = "", $logFile = LOG_FILE)
    {
        $var = \Doctrine\Common\Util\Debug::export($var, $level);
        self::dbgWriteLogVar($var, $clearLog, $prefix, $logFile);
    }

    public static function dbgPrint($var)
    {
        echo "<pre>***\n", htmlspecialchars(print_r($var, true)), "\n***\n</pre>";
    }

    public static function test()
    {
        echo "This is test";
    }
}