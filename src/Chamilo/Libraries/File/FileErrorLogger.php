<?php
namespace Chamilo\Libraries\File;

/**
 * A class which can be used to log messages and errors to seperate files
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FileErrorLogger
{
    const PROPERTY_APPEND = 'append';

    const PROPERTY_EXTENSION = 'extension';

    const PROPERTY_FILE = 'file';

    const PROPERTY_PATH = 'path';

    /**
     *
     * @var resource
     */
    private $logHandle;

    /**
     * @var resource
     */
    private $errorLogHandle;

    /**
     * @var string[]
     */
    private $logConfiguration;

    /**
     *
     * @param string[] $logConfiguration
     */
    public function __construct(array $logConfiguration)
    {
        $this->logConfiguration = $logConfiguration;
        $this->openLogFile();
        $this->openErrorLogFile();
    }

    /**
     * Closes the error log handle
     */
    public function closeErrorLogHandle()
    {
        fclose($this->errorLogHandle);
    }

    /**
     * Closes the log handle
     */
    public function closeLogHandle()
    {
        fclose($this->logHandle);
    }

    /**
     * Closes the log handles
     */
    public function closeLogHandles()
    {
        $this->closeLogHandle();
        $this->closeErrorLogHandle();
    }

    protected function getAppend()
    {
        $configuration = $this->getLogConfiguration();

        return $configuration[self::PROPERTY_APPEND] ? 'a+' : 'w+';
    }

    /**
     * Gets the current timestamp
     *
     * @return string
     */
    public function getCurrentTimestamp()
    {
        return strftime("[%d/%m/%Y - %H:%M:%S] ", time());
    }

    /**
     * @return string
     */
    protected function getErrorLogFilePath()
    {
        $configuration = $this->getLogConfiguration();

        return $configuration[self::PROPERTY_PATH] . $configuration[self::PROPERTY_FILE] . '.error.' .
            $configuration[self::PROPERTY_EXTENSION];
    }

    /**
     * @return string[]
     */
    protected function getLogConfiguration()
    {
        return $this->logConfiguration;
    }

    /**
     * @return string
     */
    protected function getLogFilePath()
    {
        $configuration = $this->getLogConfiguration();

        return $configuration[self::PROPERTY_PATH] . $configuration[self::PROPERTY_FILE] . '.' .
            $configuration[self::PROPERTY_EXTENSION];
    }

    /**
     * @param string $message
     * @param boolean $includeTimestamp
     */
    public function log(string $message, bool $includeTimestamp = true)
    {
        fwrite($this->logHandle, $this->prepareMessage($message, $includeTimestamp));
    }

    /**
     * @param string $action
     * @param string $message
     * @param boolean $includeTimestamp
     */
    public function logAction(string $action, string $message = null, bool $includeTimestamp = true)
    {
        fwrite($this->logHandle, $this->prepareActionMessage($action, $message, $includeTimestamp));
    }

    /**
     * @param string $action
     * @param string $errorMessage
     * @param boolean $includeTimestamp
     */
    public function logActionError(string $action, string $errorMessage = null, bool $includeTimestamp = true)
    {
        fwrite($this->errorLogHandle, $this->prepareActionMessage($action, $errorMessage, $includeTimestamp));
    }

    /**
     * @param string $errorMessage
     * @param boolean $includeTimestamp
     */
    public function logError(string $errorMessage, bool $includeTimestamp = true)
    {
        fwrite($this->errorLogHandle, $this->prepareMessage($errorMessage, $includeTimestamp));
    }

    /**
     * @param string $message
     * @param boolean $includeTimestamp
     */
    public function mark(string $message, bool $includeTimestamp = true)
    {
        $this->log($message, $includeTimestamp);
        $this->logError($message, $includeTimestamp);
    }

    public function openErrorLogFile()
    {
        $this->errorLogHandle = fopen($this->getErrorLogFilePath(), $this->getAppend());
    }

    public function openLogFile()
    {
        $this->logHandle = fopen($this->getLogFilePath(), $this->getAppend());
    }

    /**
     * @param string $action
     *
     * @return string
     */
    protected function prepareAction(string $action)
    {
        return '[' . str_pad(strtoupper($action), 30, ' ', STR_PAD_LEFT) . ']';
    }

    /**
     * @param string $action
     * @param string $message
     * @param boolean $includeTimestamp
     *
     * @return string
     */
    protected function prepareActionMessage(string $action, string $message = null, bool $includeTimestamp = true)
    {
        $messageParts = array();

        $messageParts[] = $this->prepareAction($action);

        if (!is_null($message))
        {
            $messageParts[] = $message;
        }

        return $this->prepareMessage(implode(' ', $messageParts), $includeTimestamp);
    }

    /**
     * @param string $message
     * @param boolean $includeTimestamp
     *
     * @return string
     */
    protected function prepareMessage(string $message, bool $includeTimestamp = true)
    {
        $message = strip_tags($message);

        if ($includeTimestamp)
        {
            $message = $this->getCurrentTimestamp() . $message;
        }

        return $message . PHP_EOL;
    }
}
