<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

/**
 * Logs Exceptions to Sentry (sentry.io)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLogger implements ExceptionLoggerInterface
{

    /**
     *
     * @var \Raven_Client
     */
    protected $sentryClient;

    /**
     * SentryExceptionLogger constructor.
     *
     * @param string $sentryConnectionString
     *
     * @throws \Exception
     */
    public function __construct($sentryConnectionString = '')
    {
        if (! class_exists('\Raven_Client'))
        {
            throw new \Exception('Can not use the SentryExceptionLogger when sentry is not included');
        }

        if (empty($sentryConnectionString))
        {
            throw new \Exception('The given connection string for sentry can not be empty');
        }

        $this->sentryClient = new \Raven_Client(
            $sentryConnectionString,
            array('install_default_breadcrumb_handlers' => false));
    }

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param int $exceptionLevel
     * @param string $file
     * @param int $line
     */
    public function logException($exception, $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, $file = null, $line = 0)
    {
        if ($exceptionLevel != self::EXCEPTION_LEVEL_FATAL_ERROR)
        {
            return;
        }

        $this->sentryClient->captureException($exception);
    }
}