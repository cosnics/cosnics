<?php
namespace Chamilo\Libraries\DependencyInjection\Traits;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Rights\Structure\Service\AuthorizationChecker;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Bridge\BridgeManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageRenderer;
use Chamilo\Libraries\Format\Structure\FooterRenderer;
use Chamilo\Libraries\Format\Structure\FooterRendererInterface;
use Chamilo\Libraries\Format\Structure\HeaderRenderer;
use Chamilo\Libraries\Format\Structure\HeaderRendererInterface;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Format\Validator\ValidatorDecorator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Libraries\Architecture\Traits
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DependencyInjectionContainerTrait
{

    protected ContainerInterface $container;

    public function getApplicationFactory(): ApplicationFactory
    {
        return $this->getService(ApplicationFactory::class);
    }

    public function getAuthorizationChecker(): AuthorizationChecker
    {
        return $this->getService(AuthorizationChecker::class);
    }

    public function getBridgeManager(): BridgeManager
    {
        return $this->getService(BridgeManager::class);
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->getService(ClassnameUtilities::class);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->getService(ConfigurablePathBuilder::class);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->getService(ConfigurationConsulter::class);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');
    }

    public function getEntityManager(): EntityManager
    {
        return $this->getService(EntityManager::class);
    }

    protected function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->getService('Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger');
    }

    public function getFooterRenderer(): FooterRendererInterface
    {
        return $this->getService(FooterRenderer::class);
    }

    public function getForm(): FormFactory
    {
        return $this->getService(FormFactory::class);
    }

    public function getGroupService(): GroupService
    {
        return $this->getService(GroupService::class);
    }

    public function getHeaderRenderer(): HeaderRendererInterface
    {
        return $this->getService(HeaderRenderer::class);
    }

    public function getLogger(): Logger
    {
        return $this->getService(Logger::class);
    }

    public function getNotificationMessageManager(): NotificationMessageManager
    {
        return $this->getService(NotificationMessageManager::class);
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->getService(NotificationMessageRenderer::class);
    }

    public function getPageConfiguration(): PageConfiguration
    {
        return $this->getService(PageConfiguration::class);
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->getService(RegistrationConsulter::class);
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->getService(ChamiloRequest::class);
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->getService(ResourceManager::class);
    }

    public function getSerializer(): Serializer
    {
        return $this->getService(Serializer::class);
    }

    /**
     * @template getService
     *
     * @param class-string<getService> $serviceId
     *
     * @return getService
     */
    public function getService(string $serviceId)
    {
        return $this->getContainer()->get($serviceId);
    }

    public function getSession(): SessionInterface
    {
        return $this->getRequest()->getSession();
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->getService(StringUtilities::class);
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->getService(SystemPathBuilder::class);
    }

    public function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return $this->getService('Chamilo\Libraries\Format\Theme\ThemeSystemPathBuilder');
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->getService('Chamilo\Libraries\Format\Theme\ThemeWebPathBuilder');
    }

    public function getTranslator(): Translator
    {
        return $this->getService(Translator::class);
    }

    public function getTwig(): Environment
    {
        return $this->getService(Environment::class);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->getService(UrlGenerator::class);
    }

    public function getUserService(): UserService
    {
        return $this->getService(UserService::class);
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->getService(UserSettingService::class);
    }

    public function getValidator(): ValidatorDecorator
    {
        return $this->getService('Symfony\Component\Validator\Validator');
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->getService(WebPathBuilder::class);
    }

    /**
     * @throws \Exception
     */
    public function initializeContainer(): void
    {
        if (!isset($this->container))
        {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }
}
