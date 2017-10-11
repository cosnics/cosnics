<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;

/**
 * Builds the exception logger(s) based on the given configuration file
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerFactory
{

    /**
     * The Chamilo Configuration
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * ExceptionLoggerFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Creates the exception logger based on the given configuration
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function createExceptionLogger()
    {
        $errorHandlingConfiguration = $this->configurationConsulter->getSetting(
            array('Chamilo\Configuration', 'error_handling'));

        $exceptionLoggerConfiguration = $errorHandlingConfiguration['exception_logger'];
        if (count($exceptionLoggerConfiguration) == 0)
        {
            return $this->createDefaultExceptionLogger();
        }

        return $this->createExceptionLoggerByConfiguration($errorHandlingConfiguration);
    }

    /**
     * Creates the default exception logger (file)
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\FileExceptionLogger
     */
    protected function createDefaultExceptionLogger()
    {
        $fileExceptionLoggerBuilder = new FileExceptionLoggerBuilder($this->configurationConsulter);

        return $fileExceptionLoggerBuilder->createExceptionLogger();
    }

    /**
     * Creates the exception logger by the given configuration
     *
     * @param string[] $errorHandlingConfiguration
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     * @throws \Exception
     */
    protected function createExceptionLoggerByConfiguration($errorHandlingConfiguration = array())
    {
        $exceptionLoggers = array();

        foreach ($errorHandlingConfiguration['exception_logger'] as $exceptionLoggerAlias => $exceptionLoggerClass)
        {
            if (! class_exists($exceptionLoggerClass))
            {
                throw new \Exception(
                    sprintf('The given exception logger class does not exist (%s)', $exceptionLoggerClass));
            }

            if (array_key_exists('exception_logger_builder', $errorHandlingConfiguration) &&
                 array_key_exists($exceptionLoggerAlias, $errorHandlingConfiguration['exception_logger_builder']))
            {
                $exceptionLoggerBuilderClass = $errorHandlingConfiguration['exception_logger_builder'][$exceptionLoggerAlias];

                if (! class_exists($exceptionLoggerBuilderClass))
                {
                    throw new \Exception(
                        sprintf(
                            'The given exception logger builder class does not exist (%s)',
                            $exceptionLoggerBuilderClass));
                }

                $exceptionLoggerBuilder = new $exceptionLoggerBuilderClass($this->configurationConsulter);

                if (! $exceptionLoggerBuilder instanceof ExceptionLoggerBuilderInterface)
                {
                    throw new \Exception(
                        sprintf(
                            'The given exception logger builder must implement the ExceptionLoggerBuilderInterface (%s)',
                            $exceptionLoggerBuilderClass));
                }

                $exceptionLogger = $exceptionLoggerBuilder->createExceptionLogger();
            }
            else
            {
                $exceptionLogger = new $exceptionLoggerClass();
            }

            if (! $exceptionLogger instanceof ExceptionLoggerInterface)
            {
                throw new \Exception(
                    sprintf(
                        'The given exception logger must implement the ExceptionLoggerInterface (%s)',
                        get_class($exceptionLogger)));
            }

            $exceptionLoggers[] = $exceptionLogger;
        }

        if (count($exceptionLoggers) == 1)
        {
            return $exceptionLoggers[0];
        }

        return new ExceptionLoggerChain($exceptionLoggers);
    }
}