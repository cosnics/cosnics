<?php
namespace Chamilo\Libraries\Architecture\Traits;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
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
     * @return \Chamilo\Libraries\Platform\ChamiloRequest | object
     */
    public function getRequest()
    {
        return $this->getService('symfony.component.http_foundation.request');
    }

    /**
     *
     * @return AuthorizationCheckerInterface | object
     */
    public function getAuthorizationChecker()
    {
        return $this->getService('chamilo.core.rights.structure.service.authorization_checker');
    }

    /**
     * @return PathBuilder | object
     */
    public function getPathBuilder()
    {
        return $this->getService('chamilo.libraries.file.path_builder');
    }

    /**
     * @return ConfigurablePathBuilder | object
     */
    public function getConfigurablePathBuilder()
    {
        return $this->getService('chamilo.libraries.file.configurable_path_builder');
    }

    /**
     * @return object | ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->getService('chamilo.configuration.service.configuration_consulter');
    }

    /**
     * @return ExceptionLoggerInterface | object
     */
    protected function getExceptionLogger()
    {
        return $this->getService('chamilo.libraries.architecture.error_handler.exception_logger');
    }

    /**
     * Returns the validator form the dependency injection container
     *
     * @return \Symfony\Component\Validator\Validator | object
     */
    public function getValidator()
    {
        return $this->getService('symfony.component.validator.validator');
    }

    /**
     * Returns the symfony form builder and renderer
     *
     * @return \Symfony\Component\Form\FormFactory | object
     */
    public function getForm()
    {
        return $this->getService('symfony.component.forms.form');
    }

    /**
     * Returns the Twig_Environment
     *
     * @return \Twig_Environment | object
     */
    public function getTwig()
    {
        return $this->getService('twig.environment');
    }

    /**
     * Returns the Monolog Logger
     *
     * @return \Monolog\Logger | object
     */
    public function getLogger()
    {
        return $this->getService('monolog.logger');
    }

    /**
     * Returns the translator
     *
     * @return \Symfony\Component\Translation\Translator | object
     */
    public function getTranslator()
    {
        return $this->getService('symfony.component.translation.translator');
    }

    /**
     * Returns the event dispatcher
     *
     * @return \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher | object
     */
    public function getEventDispatcher()
    {
        return $this->getService('symfony.component.event_dispatcher');
    }

    /**
     * Returns the serializer service
     *
     * @return \JMS\Serializer\Serializer | object
     */
    public function getSerializer()
    {
        return $this->getService('jms.serializer');
    }

    /**
     * Returns the entity manager from the dependency injection container
     *
     * @return \Doctrine\ORM\EntityManager | object
     */
    public function getEntityManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

}