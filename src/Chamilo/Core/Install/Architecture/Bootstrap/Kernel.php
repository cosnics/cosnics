<?php
namespace Chamilo\Core\Install\Architecture\Bootstrap;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Format\Response\ExceptionResponse;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Exception;

/**
 *
 * @package Chamilo\Core\Install\Architecture\Bootstrap
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Kernel
{

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    private $exceptionLogger;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
     */
    private $applicationFactory;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var integer
     */
    private $version;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Libraries\Architecture\Factory\ApplicationFactory $applicationFactory
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param integer $version
     */
    public function __construct(
        ChamiloRequest $request,
        ApplicationFactory $applicationFactory, ExceptionLoggerInterface $exceptionLogger, $version)
    {
        $this->request = $request;
        $this->applicationFactory = $applicationFactory;
        $this->exceptionLogger = $exceptionLogger;
        $this->version = $version;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
     */
    public function getApplicationFactory()
    {
        return $this->applicationFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Factory\ApplicationFactory $applicationFactory
     */
    public function setApplicationFactory(ApplicationFactory $applicationFactory)
    {
        $this->applicationFactory = $applicationFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @param integer $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function launch()
    {
        try
        {
            $this->configureContext()->buildApplication()->runApplication();
        }
        catch (UserException $exception)
        {
            $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new ExceptionResponse($exception, $this->getApplication());
            $response->send();
        }
    }

    /**
     *
     * @return \Chamilo\Core\Install\Architecture\Bootstrap\Kernel
     */
    protected function configureContext()
    {
        $this->setContext('Chamilo\Core\Install');

        return $this;
    }

    /**
     *
     * @return \Chamilo\Core\Install\Architecture\Bootstrap\Kernel
     */
    protected function buildApplication()
    {
        $context = $this->getContext();

        if (! isset($context))
        {
            throw new Exception('Must call configureContext before buildApplication');
        }

        $this->setApplication(
            $this->getApplicationFactory()->getApplication($this->getContext(), $this->getApplicationConfiguration()));

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration
     */
    protected function getApplicationConfiguration()
    {
        return new ApplicationConfiguration($this->getRequest(), null);
    }

    /**
     * Executes the application's component
     */
    protected function runApplication()
    {
        $application = $this->getApplication();

        if (! isset($application))
        {
            throw new Exception('Must call buildApplication before runApplication');
        }

        $response = $application->run();

        if (! $response instanceof Response)
        {
            $response = new Response($this->getVersion(), $response);
        }

        $response->send();
    }
}
