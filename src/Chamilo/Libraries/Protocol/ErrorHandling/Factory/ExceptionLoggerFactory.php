<?php
namespace Chamilo\Libraries\Protocol\ErrorHandling\Factory;

use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerBuilderInterface;
use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\ErrorHandling\Service\ExceptionLoggerChain;
use Chamilo\Libraries\Protocol\ErrorHandling\Service\FileExceptionLogger;
use Chamilo\Libraries\Protocol\ErrorHandling\Service\FileExceptionLoggerBuilder;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Builds the exception logger(s) based on the given configuration file
 *
 * @package Chamilo\Libraries\Protocol\ErrorHandling\Factory
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerFactory
{
    public function __construct(
        protected SessionInterface $session, protected UrlGenerator $urlGenerator,
        protected UserExceptionRendererRegistry $userExceptionRendererRegistry,
        protected array $errorHandlingConfiguration
    )
    {
    }

    /**
     * @throws \Exception
     */
    protected function createDefaultExceptionLogger(): FileExceptionLogger
    {
        $fileExceptionLoggerBuilder = new FileExceptionLoggerBuilder(
            $this->session, $this->urlGenerator, $this->userExceptionRendererRegistry,
            $this->errorHandlingConfiguration['instances']['Chamilo\Libraries\Protocol\ErrorHandling\Service\FileExceptionLoggerBuilder']
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
        $exceptionLoggerConfiguration = $this->errorHandlingConfiguration['exceptionLogger'];
        if (count($exceptionLoggerConfiguration) == 0) {
            return $this->createDefaultExceptionLogger();
        }

        return $this->createExceptionLoggerByConfiguration($this->errorHandlingConfiguration);
    }

    /**
     * Creates the exception logger by the given configuration
     *
     * @param string[][] $errorHandlingConfiguration
     *
     * @return \Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerInterface
     * @throws \Exception
     */
    protected function createExceptionLoggerByConfiguration(array $errorHandlingConfiguration = []
    ): ExceptionLoggerInterface
    {
        $exceptionLoggers = [];

        foreach ($errorHandlingConfiguration['exceptionLogger'] as $exceptionLoggerAlias => $exceptionLoggerClass) {
            if (!class_exists($exceptionLoggerClass)) {
                throw new Exception(
                    sprintf('The given exception logger class does not exist (%s)', $exceptionLoggerClass)
                );
            }

            if (array_key_exists('exceptionLoggerBuilder', $errorHandlingConfiguration) &&
                array_key_exists($exceptionLoggerAlias, $errorHandlingConfiguration['exceptionLoggerBuilder'])) {
                $exceptionLoggerBuilderClass =
                    $errorHandlingConfiguration['exceptionLoggerBuilder'][$exceptionLoggerAlias];

                if (!class_exists($exceptionLoggerBuilderClass)) {
                    throw new Exception(
                        sprintf(
                            'The given exception logger builder class does not exist (%s)', $exceptionLoggerBuilderClass
                        )
                    );
                }

                $exceptionLoggerBuilder = new $exceptionLoggerBuilderClass(
                    $this->session, $this->urlGenerator, $this->userExceptionRendererRegistry,
                    $errorHandlingConfiguration['instances'][$exceptionLoggerBuilderClass]
                );

                if (!$exceptionLoggerBuilder instanceof ExceptionLoggerBuilderInterface) {
                    throw new Exception(
                        sprintf(
                            'The given exception logger builder must implement the ExceptionLoggerBuilderInterface (%s)',
                            $exceptionLoggerBuilderClass
                        )
                    );
                }

                $exceptionLogger = $exceptionLoggerBuilder->createExceptionLogger();
            }
            else {
                $exceptionLogger = new $exceptionLoggerClass();
            }

            if (!$exceptionLogger instanceof ExceptionLoggerInterface) {
                throw new Exception(
                    sprintf(
                        'The given exception logger must implement the ExceptionLoggerInterface (%s)',
                        get_class($exceptionLogger)
                    )
                );
            }

            $exceptionLoggers[] = $exceptionLogger;
        }

        if (count($exceptionLoggers) == 1) {
            return $exceptionLoggers[0];
        }

        return new ExceptionLoggerChain($exceptionLoggers);
    }
}