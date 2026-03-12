<?php
namespace Chamilo\Libraries\DependencyInjection\Architecture\Trait;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\DependencyInjection\Service\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\DependencyInjection\Architecture\Trait
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DependencyInjectionContainerTrait
{
    protected ContainerInterface $container;

    public function getAlertRenderer(): AlertRenderer
    {
        return $this->getService(AlertRenderer::class);
    }

    public function getAlertsManager(): AlertsManager
    {
        return $this->getService(AlertsManager::class);
    }

    public function getApplicationHeaderRenderer(): ApplicationHeaderRenderer
    {
        return $this->getService(ApplicationHeaderRenderer::class);
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->getService(BreadcrumbTrail::class);
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->getService(ClassnameUtilities::class);
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

    public function getDefaultFooterRenderer(): DefaultFooterRenderer
    {
        return $this->getService(DefaultFooterRenderer::class);
    }

    public function getGroupService(): GroupService
    {
        return $this->getService(GroupService::class);
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->getService(ChamiloRequest::class);
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->getService(ResourceManager::class);
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

    public function getStringUtilities(): StringUtilities
    {
        return $this->getService(StringUtilities::class);
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->getService(SystemPathBuilder::class);
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
