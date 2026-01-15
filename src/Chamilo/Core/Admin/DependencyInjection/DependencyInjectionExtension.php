<?php
namespace Chamilo\Core\Admin\DependencyInjection;

use Chamilo\Core\Admin\DependencyInjection\CompilerPass\ActionProviderCompilerPass;
use Chamilo\Core\Admin\DependencyInjection\CompilerPass\SettingsConnectorsCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Admin\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.admin';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Admin' => [
                'architecture.domain.xml',
                'implementation.admin.xml',
                'implementation.home.xml',
                'service.xml',
                'storage.xml',
                'userInterface.table.xml'
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ActionProviderCompilerPass());
        $container->addCompilerPass(new SettingsConnectorsCompilerPass());
    }
}