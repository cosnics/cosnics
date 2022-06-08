<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Exception;

/**
 * Builds the SentryExceptionLogger class
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{

    protected ConfigurationConsulter $configurationConsulter;

    protected SessionUtilities $sessionUtilities;

    public function __construct(ConfigurationConsulter $configurationConsulter, SessionUtilities $sessionUtilities)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): SentryExceptionLogger
    {
        $clientDSNKey = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'error_handling', 'sentry_error_logger', 'DSN')
        );

        if (empty($clientDSNKey))
        {
            throw new Exception(
                'The DSN key should be configured when using the sentry exception logger. ' .
                'The configuration should be put in ' .
                'chamilo.configuration.error_handling["sentry_error_logger"]["DSN"]'
            );
        }

        return new SentryExceptionLogger($this->getSessionUtilities(), $clientDSNKey);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }
}