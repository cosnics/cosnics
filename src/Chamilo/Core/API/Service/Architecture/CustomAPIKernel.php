<?php

namespace Chamilo\Core\API\Service\Architecture;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\API\Service\Architecture\Routing\APIRouteMatcher;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Bootstrap\Kernel;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException;
use Chamilo\Libraries\Architecture\Exceptions\PlatformNotAvailableException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CustomAPIKernel extends Kernel
{
    protected APIRouteMatcher $routeMatcher;

    public function __construct(
        \Chamilo\Libraries\Platform\ChamiloRequest $request,
        ConfigurationConsulter $configurationConsulter, ApplicationFactory $applicationFactory,
        SessionUtilities $sessionUtilities, ExceptionLoggerInterface $exceptionLogger,
        AuthenticationValidator $authenticationValidator, $version, APIRouteMatcher $routeMatcher, User $user = null
    )
    {
        parent::__construct($request, $configurationConsulter, $applicationFactory, $sessionUtilities, $exceptionLogger, $authenticationValidator, $version, $user);
        $this->routeMatcher = $routeMatcher;
    }

    public function launch(): void
    {
        try
        {
            $this->configureTimezone()->checkAuthentication()
                ->checkPlatformAvailability()->buildApplication()->runApplication();
        }
        catch (NotAuthenticatedException $exception)
        {
        }
        catch (PlatformNotAvailableException $exception)
        {
        }
        catch (UserException $exception)
        {
        }
    }

    protected function checkAuthentication(): CustomAPIKernel
    {
        //todo: implement oauth2 authentication

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