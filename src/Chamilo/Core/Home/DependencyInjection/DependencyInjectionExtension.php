<?php
namespace Chamilo\Core\Home\DependencyInjection;

use Chamilo\Core\Home\DependencyInjection\CompilerPass\AvailableBlockRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Home\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.home';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Home' => [
                'architecture.domain.xml',
                'service.xml',
                'storage.xml',
                'userInterface.homeRenderer.xml',
                'userInterface.table.xml'
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AvailableBlockRendererCompilerPass());
    }
}