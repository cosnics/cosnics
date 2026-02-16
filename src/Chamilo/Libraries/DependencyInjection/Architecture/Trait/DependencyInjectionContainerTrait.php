<?php
namespace Chamilo\Libraries\DependencyInjection\Architecture\Trait;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\DependencyInjection\Service\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Service\Bootstrap\ApplicationFactory;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultHeaderRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageManager;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageRenderer;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\DependencyInjection\Architecture\Trait
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DependencyInjectionContainerTrait
{
    protected ContainerInterface $container;

    public function getApplicationFactory(): ApplicationFactory
    {
        return $this->getService(ApplicationFactory::class);
    }

    public function getApplicationHeaderRenderer(): ApplicationHeaderRenderer
    {
        return $this->getService(ApplicationHeaderRenderer::class);
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->getService(BreadcrumbTrail::class);
    }

    public function getButtonToolBarRenderer(): ButtonToolBarRenderer
    {
        return $this->getService(ButtonToolBarRenderer::class);
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->getService(ClassnameUtilities::class);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->getService(ConfigurablePathBuilder::class);
    }

    public function getContainer(): ContainerInterface
    {
        if (!isset($this->container)) {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }

        return $this->container;
    }

    public function setContainer(ContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Repository\DataClassRepository> $className
     */
    protected function getDataClassRepository(
        string $className = 'Chamilo\Libraries\Storage\Implementations\Doctrine\Repository\DataClassRepository'
    ): DataClassRepository
    {
        return $this->getService($className);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->getService(DatetimeUtilities::class);
    }

    public function getDefaultFooterRenderer(): DefaultFooterRenderer
    {
        return $this->getService(DefaultFooterRenderer::class);
    }

    public function getDefaultHeaderRenderer(): DefaultHeaderRenderer
    {
        return $this->getService(DefaultHeaderRenderer::class);
    }

    /**
     * @param class-string<\Symfony\Component\EventDispatcher\EventDispatcherInterface> $className
     */
    public function getEventDispatcher(string $className = EventDispatcher::class): EventDispatcherInterface
    {
        return $this->getService($className);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface> $className
     */
    protected function getExceptionLogger(
        string $className = 'Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger'
    ): ExceptionLoggerInterface
    {
        return $this->getService($className);
    }

    public function getFilesystem(): Filesystem
    {
        return $this->getService(Filesystem::class);
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->getService(FilesystemTools::class);
    }

    public function getGroupService(): GroupService
    {
        return $this->getService(GroupService::class);
    }

    public function getNotificationMessageManager(): NotificationMessageManager
    {
        return $this->getService(NotificationMessageManager::class);
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->getService(NotificationMessageRenderer::class);
    }

    public function getPageConfiguration(): PageHeaders
    {
        return $this->getService(PageHeaders::class);
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

    /**
     * @param class-string<\Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder> $className
     */
    public function getThemeSystemPathBuilder(
        string $className = 'Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder'
    ): ThemePathBuilder
    {
        return $this->getService($className);
    }

    /**
     * @param class-string<\Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder> $className
     */
    public function getThemeWebPathBuilder(
        string $className = 'Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder'
    ): ThemePathBuilder
    {
        return $this->getService($className);
    }

    public function getTranslator(): Translator
    {
        return $this->getService(Translator::class);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->getService(UrlGenerator::class);
    }

    public function getUserService(): UserService
    {
        return $this->getService(UserService::class);
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->getService(WebPathBuilder::class);
    }
}
