<?php
namespace Chamilo\Libraries\File;

/**
 * A class which can be used to log messages to a file
 *
 * @package Chamilo\Libraries\File
 * @author  Vanpoucke Sven
 */
class FileLogger
{

    /**
     * @var resource
     */
    private $handle;

    /**
     * @param string $file
     * @param bool $append
     */
    public function __construct($file, $append = false)
    {
        $mode = $append ? 'a+' : 'w+';
        $this->open_file($file, $mode);
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
     * @return int
     */
    public function get_timestamp()
    {
        $timestamp = strftime('[%d/%m/%Y - %H:%M:%S] ', time());

        return $timestamp;
    }

    /**
     * Logs a message to the file
     *
     * @param string $message
     * @param bool $includeTimestamp
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
