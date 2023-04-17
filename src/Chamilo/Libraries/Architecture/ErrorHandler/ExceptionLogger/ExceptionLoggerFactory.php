<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Exception;

/**
 * Builds the exception logger(s) based on the given configuration file
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerFactory
{

    protected ConfigurationConsulter $configurationConsulter;

    protected SessionUtilities $sessionUtilities;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, SessionUtilities $sessionUtilities, UrlGenerator $urlGenerator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->sessionUtilities = $sessionUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Exception
     */
    protected function createDefaultExceptionLogger(): FileExceptionLogger
    {
        $fileExceptionLoggerBuilder = new FileExceptionLoggerBuilder(
            $this->getConfigurationConsulter(), $this->getSessionUtilities(), $this->getUrlGenerator()
        );

        return $fileExceptionLoggerBuilder->createExceptionLogger();
    }

    /**
     * Creates the exception logger based on the given configuration
     *
     * @throws \Exception
     */
    public function createExceptionLogger(): ExceptionLoggerInterface
    {
        $errorHandlingConfiguration = $this->configurationConsulter->getSetting(
            ['Chamilo\Configuration', 'error_handling']
        );

        $exceptionLoggerConfiguration = $errorHandlingConfiguration['exception_logger'];
        if (count($exceptionLoggerConfiguration) == 0)
        {
            return $this->createDefaultExceptionLogger();
        }

        return $this->createExceptionLoggerByConfiguration($errorHandlingConfiguration);
    }

    /**
     * Creates the exception logger by the given configuration
     *
     * @param string[][] $errorHandlingConfiguration
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     * @throws \Exception
     */
    protected function createExceptionLoggerByConfiguration(array $errorHandlingConfiguration = [])
    {
        $exceptionLoggers = [];

        foreach ($errorHandlingConfiguration['exception_logger'] as $exceptionLoggerAlias => $exceptionLoggerClass)
        {
            if (!class_exists($exceptionLoggerClass))
            {
                throw new Exception(
                    sprintf('The given exception logger class does not exist (%s)', $exceptionLoggerClass)
                );
            }

            if (array_key_exists('exception_logger_builder', $errorHandlingConfiguration) &&
                array_key_exists($exceptionLoggerAlias, $errorHandlingConfiguration['exception_logger_builder']))
            {
                $exceptionLoggerBuilderClass =
                    $errorHandlingConfiguration['exception_logger_builder'][$exceptionLoggerAlias];

                if (!class_exists($exceptionLoggerBuilderClass))
                {
                    throw new Exception(
                        sprintf(
                            'The given exception logger builder class does not exist (%s)', $exceptionLoggerBuilderClass
                        )
                    );
                }

                $exceptionLoggerBuilder = new $exceptionLoggerBuilderClass(
                    $this->getConfigurationConsulter(), $this->getSessionUtilities(), $this->getUrlGenerator()
                );

                if (!$exceptionLoggerBuilder instanceof ExceptionLoggerBuilderInterface)
                {
                    throw new Exception(
                        sprintf(
                            'The given exception logger builder must implement the ExceptionLoggerBuilderInterface (%s)',
                            $exceptionLoggerBuilderClass
                        )
                    );
                }

                $exceptionLogger = $exceptionLoggerBuilder->createExceptionLogger();
            }
            else
            {
                $exceptionLogger = new $exceptionLoggerClass();
            }

            if (!$exceptionLogger instanceof ExceptionLoggerInterface)
            {
                throw new Exception(
                    sprintf(
                        'The given exception logger must implement the ExceptionLoggerInterface (%s)',
                        get_class($exceptionLogger)
                    )
                );
            }

            $exceptionLoggers[] = $exceptionLogger;
        }

        if (count($exceptionLoggers) == 1)
        {
            return $exceptionLoggers[0];
        }

        return new ExceptionLoggerChain($exceptionLoggers);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}