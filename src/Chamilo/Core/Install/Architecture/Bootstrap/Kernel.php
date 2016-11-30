<?php
namespace Chamilo\Core\Install\Architecture\Bootstrap;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Format\Response\ExceptionResponse;
use Chamilo\Libraries\Format\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Format\Response\Response;

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
     * @var \Symfony\Component\HttpFoundation\Request
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
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    public function __construct(\Symfony\Component\HttpFoundation\Request $request,
        ApplicationFactory $applicationFactory, ExceptionLoggerInterface $exceptionLogger)
    {
        $this->request = $request;
        $this->applicationFactory = $applicationFactory;
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
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

    public function launch()
    {
        try
        {
            $this->configureContext()->buildApplication()->runApplication();
        }
        catch (NotAuthenticatedException $exception)
        {
            $response = $this->getNotAuthenticatedResponse();
            $response->send();
        }
        catch (UserException $exception)
        {
            $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new ExceptionResponse($exception, $this->getApplication());
            $response->send();
        }
    }

    /**
     * Returns a response that renders the not authenticated message
     *
     * @return NotAuthenticatedResponse
     */
    protected function getNotAuthenticatedResponse()
    {
        return new NotAuthenticatedResponse();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     */
    protected function configureContext()
    {
        $getContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);

        if (! $getContext)
        {
            $postContext = $this->getRequest()->request->get(Application::PARAM_CONTEXT);

            if (! $postContext)
            {
                $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Home');

                $context = 'Chamilo\Core\Home';
            }
            else
            {
                $context = $postContext;
            }
        }
        else
        {
            $context = $getContext;
        }

        $this->setContext(Application::context_fallback($context, $this->getFallbackContexts()));

        return $this;
    }

    /**
     * Returns a list of the available fallback contexts
     *
     * @return array
     */
    protected function getFallbackContexts()
    {
        $fallbackContexts = array();
        $fallbackContexts[] = 'Chamilo\Application\\';
        $fallbackContexts[] = 'Chamilo\Core\\';
        $fallbackContexts[] = 'Chamilo\\';

        return $fallbackContexts;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     */
    protected function buildApplication()
    {
        $context = $this->getContext();

        if (! isset($context))
        {
            throw new \Exception('Must call configureContext before buildApplication');
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
            throw new \Exception('Must call buildApplication before runApplication');
        }

        $response = $application->run();

        if (! $response instanceof Response)
        {
            $response = new Response($response);
        }

        $response->send();
    }
}
