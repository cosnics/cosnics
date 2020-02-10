<?php
namespace Chamilo\Libraries\Architecture\Traits;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Rights\Structure\Service\AuthorizationChecker;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Chamilo\Libraries\Architecture\Bridge\BridgeManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DependencyInjectionContainerTrait
{

    /**
     * The dependency injection container
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
     */
    public function getApplicationFactory()
    {
        return $this->getService(ApplicationFactory::class);
    }

    /**
     *
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker()
    {
        return $this->getService(AuthorizationChecker::class);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Bridge\BridgeManager
     */
    public function getBridgeManager()
    {
        return $this->getService(BridgeManager::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->getService(ClassnameUtilities::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder()
    {
        return $this->getService(ConfigurablePathBuilder::class);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->getService(ConfigurationConsulter::class);
    }

    /**
     * Returns the dependency injection container
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the dependency injection container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');
    }

    /**
     * Returns the entity manager from the dependency injection container
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getService(EntityManager::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected function getExceptionLogger()
    {
        return $this->getService('Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger');
    }

    /**
     * Returns the symfony form builder and renderer
     *
     * @return \Symfony\Component\Form\FormFactory
     */
    public function getForm()
    {
        return $this->getService(FormFactory::class);
    }

    /**
     *
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService()
    {
        return $this->getService(GroupService::class);
    }

    /**
     * Returns the Monolog Logger
     *
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->getService(Logger::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->getService(PathBuilder::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Utilities\ResourceManager
     */
    public function getResourceManager()
    {
        return $this->getService(ResourceManager::class);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter()
    {
        return $this->getService(RegistrationConsulter::class);
    }

    /**
     * Returns the request
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->getService(ChamiloRequest::class);
    }

    /**
     * Returns the serializer service
     *
     * @return \JMS\Serializer\Serializer
     */
    public function getSerializer()
    {
        return $this->getService(Serializer::class);
    }

    /**
     * Returns a service from the dependency injection container
     *
     * @param string $serviceId
     *
     * @return mixed
     */
    public function getService($serviceId)
    {
        return $this->getContainer()->get($serviceId);
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    public function getSessionUtilities()
    {
        return $this->getService(SessionUtilities::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->getService(StringUtilities::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities()
    {
        return $this->getService(Theme::class);
    }

    /**
     * Returns the translator
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->getService(Translator::class);
    }

    /**
     * Returns the Twig_Environment
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->getService(\Twig\Environment::class);
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->getService(UserService::class);
    }

    /**
     * Returns the validator form the dependency injection container
     *
     * @return \Chamilo\Libraries\Format\Validator\ValidatorDecorator
     */
    public function getValidator()
    {
        return $this->getService('Symfony\Component\Validator\Validator');
    }

    /**
     * Initializes the container
     */
    public function initializeContainer()
    {
        if (!isset($this->container))
        {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }
    }
}
