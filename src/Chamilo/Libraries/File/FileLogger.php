<?php
namespace Chamilo\Libraries\File;

/**
 * A class which can be used to log messages to a file
 *
 * @package Chamilo\Libraries\File
 * @author Vanpoucke Sven
 */
class FileLogger
{

    /**
     *
     * @var resource
     */
    private $handle;

    /**
     *
     * @param string $file
     * @param boolean $append
     */
    public function __construct($file, $append = false)
    {
        $mode = $append ? 'a+' : 'w+';
        $this->open_file($file, $mode);
    }

    /**
     *
     * @param string[] $trace
     */
    public function call_trace($trace)
    {
        $logfile = Path::getInstance()->getLogPath() . '/call_errors.log';
        $logger = new self($logfile, true);

        $i = 0;

        while (!isset($trace[$i]['line']))
        {
            $i ++;
        }

        $message = '[' . $trace[0]['class'] . '] [' . $trace[$i]['line'] . '] ==> ' . $trace[$i]['file'];

        $logger->log_message($message);
    }

    /**
     * Closes the file handle
     */
    public function close_file()
    {
        fclose($this->handle);
    }

    /**
     * Gets the current timestamp
     *
     * @return integer
     */
    public function get_timestamp()
    {
        $timestamp = strftime("[%d/%m/%Y - %H:%M:%S] ", time());

        return $timestamp;
    }

    /**
     * Logs a message to the file
     *
     * @param string $message
     * @param boolean $includeTimestamp
     */
    public function log_message($message, $includeTimestamp = true)
    {
        $message = strip_tags($message);

        if ($includeTimestamp)
        {
            $message = $this->get_timestamp() . $message;
        }

        fwrite($this->handle, $message . PHP_EOL);
    }

    /**
     * Opens the given file
     *
     * @param string $file
     * @param string $mode
     */
    public function open_file($file, $mode)
    {
        $this->handle = fopen($file, $mode);
    }
}
