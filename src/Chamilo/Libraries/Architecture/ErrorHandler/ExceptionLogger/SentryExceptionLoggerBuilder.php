<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Configuration;

/*
 * Builds the SentryExceptionLogger class
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{

    /**
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * ExceptionLoggerBuilderInterface constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Creates the exception logger
     *
     * @return ExceptionLoggerInterface
     *
     * @throws \Exception
     */
    public function createExceptionLogger()
    {
        $clientDSNKey = $this->configuration->get_setting(array('Chamilo\Configuration', 'sentry_error_logger', 'DSN'));

        if (empty($clientDSNKey))
        {
            throw new \Exception(
                'The DSN key should be configured when using the sentry exception logger. ' .
                     'The configuration should be put in exception_logger_parameters["sentry"]["DSN"]');
        }

        return new SentryExceptionLogger($clientDSNKey);
    }
}