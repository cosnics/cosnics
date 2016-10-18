<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Configuration;

/**
 * Builds the exception logger(s) based on the given configuration file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerFactory
{
    /**
     * The Chamilo Configuration
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * ExceptionLoggerFactory constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Creates the exception logger based on the given configuration
     *
     * @return ExceptionLoggerInterface
     */
    public function createExceptionLogger()
    {
        $errorHandlingConfiguration = $this->configuration->get_setting(
            array('Chamilo\Configuration', 'error_handling')
        );

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
     * @return FileExceptionLogger
     */
    protected function createDefaultExceptionLogger()
    {
        $fileExceptionLoggerBuilder = new FileExceptionLoggerBuilder($this->configuration);

        return $fileExceptionLoggerBuilder->createExceptionLogger();
    }

    /**
     * Creates the exception logger by the given configuration
     *
     * @param array $errorHandlingConfiguration
     *
     * @return ExceptionLoggerInterface
     *
     * @throws \Exception
     */
    protected function createExceptionLoggerByConfiguration($errorHandlingConfiguration = array())
    {
        $exceptionLoggers = array();

        foreach ($errorHandlingConfiguration['exception_logger'] as $exceptionLoggerAlias => $exceptionLoggerClass)
        {
            if (!class_exists($exceptionLoggerClass))
            {
                throw new \Exception(
                    sprintf('The given exception logger class does not exist (%s)', $exceptionLoggerClass)
                );
            }

            if (array_key_exists('exception_logger_builder', $errorHandlingConfiguration) &&
                array_key_exists($exceptionLoggerAlias, $errorHandlingConfiguration['exception_logger_builder'])
            )
            {
                $exceptionLoggerBuilderClass =
                    $errorHandlingConfiguration['exception_logger_builder'][$exceptionLoggerAlias];

                if (!class_exists($exceptionLoggerBuilderClass))
                {
                    throw new \Exception(
                        sprintf(
                            'The given exception logger builder class does not exist (%s)', $exceptionLoggerBuilderClass
                        )
                    );
                }

                $exceptionLoggerBuilder = new $exceptionLoggerBuilderClass($this->configuration);

                if (!$exceptionLoggerBuilder instanceof ExceptionLoggerBuilderInterface)
                {
                    throw new \Exception(
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

            if(!$exceptionLogger instanceof ExceptionLoggerInterface)
            {
                throw new \Exception(
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
}