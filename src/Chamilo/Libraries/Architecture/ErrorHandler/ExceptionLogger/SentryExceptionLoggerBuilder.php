<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Service\ConfigurationConsulter;

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
    protected $configurationConsulter;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerBuilderInterface::__construct()
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
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
        $clientDSNKey = $this->configurationConsulter->getSetting(
            array('Chamilo\Configuration', 'error_handling', 'sentry_error_logger', 'DSN')
        );

        if (empty($clientDSNKey))
        {
            throw new \Exception(
                'The DSN key should be configured when using the sentry exception logger. ' .
                'The configuration should be put in ' .
                'chamilo.configuration.error_handling["sentry_error_logger"]["DSN"]'
            );
        }

        return new SentryExceptionLogger($clientDSNKey);
    }
}