<?php
namespace Chamilo\Core\Menu\DependencyInjection;

use Chamilo\Core\Menu\DependencyInjection\CompilerPass\AvailableItemRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Menu\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.menu';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Menu' => [
                'architecture.domain.xml',
                'implementation.admin.xml',
                'implementation.menu.xml',
                'service.xml',
                'storage.xml',
                'userInterface.menuRenderer.xml',
                'userInterface.table.xml'
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AvailableItemRendererCompilerPass());
    }
}