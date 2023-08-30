<?php
namespace Chamilo\Core\Notification\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Chamilo\Libraries\DependencyInjection\Traits\IConfigurableExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Notification\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, IConfigurableExtension
{
    use ExtensionTrait;
    use IConfigurableExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.notification';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Notification' => ['package.xml', 'menu.xml', 'services.xml']];
    }

    public function getContainerConfigurationFiles(): array
    {
        return ['Chamilo\Core\Notification' => ['Config.yml']];
    }
}