<?php
namespace Chamilo\Core\Menu\DependencyInjection;

use Chamilo\Core\Menu\DependencyInjection\CompilerPass\AvailableItemRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
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
                'architecture.php',
                'implementation.admin.php',
                'implementation.menu.php',
                'service.php',
                'storage.php',
                'userInterface.php',
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AvailableItemRendererCompilerPass());
    }
}