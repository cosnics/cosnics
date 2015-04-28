<?php
namespace Chamilo\Libraries\File;

/**
 * A class which can be used to log messages to a file
 * 
 * @author Vanpoucke Sven
 */
class FileLogger
{

    private $handle;

    /**
     * Constructor
     * 
     * @param $file String - The full path to the file
     * @param $append Bool - create a new file, or append to existing one
     */
    public function __construct($file, $append = false)
    {
        $mode = $append ? 'a+' : 'w+';
        $this->open_file($file, $mode);
    }

    /**
     * Opens the given file
     * 
     * @param $file - The full path to the file
     * @param $mode - The mode to open the file
     */
    public function open_file($file, $mode)
    {
        $this->handle = fopen($file, $mode);
    }

    /**
     * Closes the file handle
     */
    public function close_file()
    {
        fclose($this->handle);
    }

    /**
     * Logs a message to the file
     * 
     * @param $message String
     * @param $include_timestamp Bool
     */
    public function log_message($message, $include_timestamp = true)
    {
        $message = strip_tags($message);
        
        if ($include_timestamp)
        {
            $message = $this->get_timestamp() . $message;
        }
        
        fwrite($this->handle, $message . "\n");
    }

    /**
     * Get's the current timestamp
     */
    public function get_timestamp()
    {
        $timestamp = strftime("[%d/%m/%Y - %H:%M:%S] ", time());
        return $timestamp;
    }

    public function call_trace($trace)
    {
        $logfile = Path :: getInstance()->getLogPath() . '/call_errors.log';
        $logger = new self($logfile, true);
        
        $i = 0;
        
        while (! isset($trace[$i]['line']))
        {
            $i ++;
        }
        
        $message = '[' . $trace[0]['class'] . '] [' . $trace[$i]['line'] . '] ==> ' . $trace[$i]['file'];
        
        $logger->log_message($message);
    }
}
