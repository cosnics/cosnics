<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler;

/**
 * Interface for services that can handle errors
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler
 */
interface ErrorHandlerInterface
{
    /**
     * Handles a fatal error
     *
     * @param $fatalErrorMessage
     * @param $file
     * @param $line
     *
     * @return
     */
    public function handleFatalError($fatalErrorMessage, $file, $line);

    /**
     * Handles a catchable error
     *
     * @param int $errorNumber
     * @param string $errorString
     * @param string $file
     * @param int $line
     */
    public function handleError($errorNumber, $errorString, $file, $line);

    /**
     * Handles an exception
     *
     * @param \Exception $exception
     */
    public function handleException($exception);
}