<?php
namespace Chamilo\Core\Admin\DependencyInjection;

use Chamilo\Core\Admin\DependencyInjection\CompilerPass\ActionProviderCompilerPass;
use Chamilo\Core\Admin\DependencyInjection\CompilerPass\SettingsConnectorsCompilerPass;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\Admin\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait {
        load as public extensionLoad;
    }

    public function getAlias(): string
    {
        return 'chamilo.core.admin';
    }

    public function getConfigurationFiles(): array
    {
        return [
            Manager::CONTEXT => [
                'application.php',
                'architecture.php',
                'implementation.admin.php',
                'implementation.home.php',
                'service.php',
                'storage.php',
                'userInterface.php'
            ]
        ];
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->extensionLoad($configs, $container);

        $yamlFileLoader = new YamlFileLoader(
            $container, new FileLocator($this->getSystemPathBuilder()->getConfigurationStoragePath())
        );
        $yamlFileLoader->load('configuration.yaml');
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ActionProviderCompilerPass());
        $container->addCompilerPass(new SettingsConnectorsCompilerPass());
    }
}