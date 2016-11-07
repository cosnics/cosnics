<?php
namespace Chamilo\Libraries\Architecture\Traits;

use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits$DependencyInjectionContainerTrait
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DependencyInjectionContainerTrait
{

    /**
     * The dependency injection container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Initializes the container
     */
    public function initializeContainer()
    {
        if (! isset($this->container))
        {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }
    }

    /**
     * Sets the dependency injection container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the dependency injection container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns a service from the dependency injection container
     *
     * @param string $service_id
     *
     * @return object
     */
    public function getService($service_id)
    {
        return $this->getContainer()->get($service_id);
    }

    /**
     * Returns the request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getService('symfony.component.http_foundation.request');
    }

    /**
     *
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker()
    {
        return $this->getService('chamilo.core.rights.structure.service.authorization_checker');
    }
}