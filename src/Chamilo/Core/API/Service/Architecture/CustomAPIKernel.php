<?php

namespace Chamilo\Core\API\Service\Architecture;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\API\Service\Architecture\Routing\APIRouteMatcher;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Bootstrap\Kernel;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\PlatformNotAvailableException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CustomAPIKernel extends Kernel
{
    protected APIRouteMatcher $routeMatcher;
    protected ResourceServer $resourceServer;

    public function __construct(
        \Chamilo\Libraries\Platform\ChamiloRequest $request,
        ConfigurationConsulter $configurationConsulter, ApplicationFactory $applicationFactory,
        SessionUtilities $sessionUtilities, ExceptionLoggerInterface $exceptionLogger,
        AuthenticationValidator $authenticationValidator, $version, APIRouteMatcher $routeMatcher,
        ResourceServer $resourceServer, User $user = null
    )
    {
        parent::__construct($request, $configurationConsulter, $applicationFactory, $sessionUtilities, $exceptionLogger, $authenticationValidator, $version, $user);
        $this->routeMatcher = $routeMatcher;
        $this->resourceServer = $resourceServer;
    }

    public function launch(): void
    {
        try
        {
            $this->configureTimezone()
                ->checkPlatformAvailability()->buildApplication()->checkAuthentication()->runApplication();
        }
        catch (NotAuthenticatedException|NotAllowedException $exception)
        {
            $response = new JsonResponse(['error' => 'You are not allowed to access this resources.'], 403);
            $response->send();
            return;
        }
        catch (PlatformNotAvailableException $exception)
        {
            $response = new JsonResponse(['error' => 'Platform is not available due to maintenance. Please try again later'], 503);
            $response->send();
            return;
        }
        catch(ResourceNotFoundException|ObjectNotExistException $exception)
        {
            $response = new JsonResponse(['error' => 'Resource not found'], 404);
            $response->send();
            return;
        }
        catch (UserException $exception)
        {
            $response = new JsonResponse(['error' => $exception->getMessage()], 500);
            $response->send();
            return;
        }
        catch(\Exception $exception)
        {
            var_dump($exception);

            $response = new JsonResponse(['error' => 'An error occurred. Please try again later'], 500);
            $response->send();
            return;
        }
    }

    protected function checkAuthentication(): CustomAPIKernel
    {
        if($this->getApplication() instanceof NoAuthenticationSupport)
        {
            return $this;
        }

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        try
        {
            $this->resourceServer->validateAuthenticatedRequest($psrHttpFactory->createRequest($this->getRequest()));
        }
        catch(\Exception)
        {
            throw new NotAuthenticatedException('You are not authenticated');
        }

        return $this;
    }

    protected function buildApplication(): CustomAPIKernel
    {
        $routeParameters = $this->routeMatcher->match($this->getRequest());
        $class = $routeParameters['_controller'];

        $application = new $class($this->getApplicationConfiguration());
        $this->setApplication($application);

        foreach($routeParameters as $parameter => $value)
        {
            if($parameter !== '_controller' && $parameter !== '_route')
            {
                $application->set_parameter($parameter, $value);
            }
        }

        return $this;
    }
}