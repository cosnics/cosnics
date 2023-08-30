<?php
namespace Chamilo\Core\Queue\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Chamilo\Libraries\DependencyInjection\Traits\IConfigurableExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Queue\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, IConfigurableExtension
{
    use ExtensionTrait;
    use IConfigurableExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.queue';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Queue' => ['package.xml', 'services.xml']];
    }

    public function getContainerConfigurationFiles(): array
    {
        return ['Chamilo\Core\Queue' => ['Config.yml']];
    }
}