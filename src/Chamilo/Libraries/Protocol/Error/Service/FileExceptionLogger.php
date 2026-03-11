<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Exception;
use Throwable;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLogger implements ExceptionLoggerInterface
{
    protected string $logPath;

    protected UserExceptionRendererRegistry $userExceptionRendererRegistry;

    /**
     * @throws \Exception
     */
    public function __construct(UserExceptionRendererRegistry $userExceptionRendererRegistry, string $logPath)
    {
        if (empty($logPath)) {
            throw new Exception('The given log path can not be empty');
        }

        if (!file_exists($logPath) || !is_dir($logPath) || !is_writable($logPath)) {
            throw new Exception(
                sprintf('The given log path either does not exist or is not a valid directory. (%s)', $logPath)
            );
        }

        $this->logPath = $logPath;
        $this->userExceptionRendererRegistry = $userExceptionRendererRegistry;
    }

    public function addJavascriptExceptionLogger(PageHeaders $pageConfiguration)
    {
    }

    protected function determineExceptionLevelString(int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR): string
    {
        switch ($exceptionLevel) {
            case self::EXCEPTION_LEVEL_WARNING :
                return 'WARNING';
            case self::EXCEPTION_LEVEL_ERROR :
                return 'ERROR';
            case self::EXCEPTION_LEVEL_FATAL_ERROR :
                return 'FATAL';
            default :
                return '[ERROR]';
        }
    }

    public function getLogPath(): string
    {
        return $this->logPath;
    }

    public function getUserExceptionRendererRegistry(): UserExceptionRendererRegistry
    {
        return $this->userExceptionRendererRegistry;
    }

    /**
     * @throws \Exception
     */
    public function logException(
        Throwable $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    ): void
    {
        if ($exceptionLevel == self::EXCEPTION_LEVEL_WARNING) {
            return;
        }

        $logFile = $this->getLogPath() . DIRECTORY_SEPARATOR . 'cosnics.error.fatal.log';
        $fileHandler = fopen($logFile, 'a');

        $type = $this->determineExceptionLevelString($exceptionLevel);

        if ($exception instanceof UserExceptionInterface) {
            $userExceptionRenderer =
                $this->getUserExceptionRendererRegistry()->getUserExceptionRendererForUserException($exception);
            $exceptionMessage = $userExceptionRenderer->renderMessage($exception);
        }
        else {
            $exceptionMessage = $exception->getMessage();
        }

        $message = date('[d/m/Y - H:i:s] ', time()) . ' - [' . $type . '] ' . $exceptionMessage;

        if (!is_null($file)) {
            $message .= ' - FILE: ' . $file . ' - LINE: ' . $line;
        }
        elseif ($exception->getFile()) {
            $message .= ' - FILE: ' . $exception->getFile() . ' - LINE: ' . $exception->getLine();
        }

        $traceString = $exception->getTraceAsString();

        if ($traceString) {
            $message .= PHP_EOL . $traceString;
        }

        fwrite($fileHandler, $message . PHP_EOL);
        fclose($fileHandler);
    }
}