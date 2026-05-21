<?php
namespace Chamilo\Core\User\DependencyInjection;

use Chamilo\Core\User\DependencyInjection\CompilerPass\UserDetailsRendererCompilerPass;
use Chamilo\Core\User\DependencyInjection\CompilerPass\UserPictureProviderCompilerPass;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\IConfigurableExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\User\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension, IConfigurableExtension
{
    use ExtensionTrait {
        load as public extensionLoad;
    }

    public function getAlias(): string
    {
        return 'chamilo.core.user';
    }

    public function getConfigurationFiles(): array
    {
        return [
            Manager::CONTEXT => [
                'application.php',
                'architecture.php',
                'implementation.admin.php',
                'implementation.home.php',
                'implementation.menu.php',
                'implementation.user.php',
                'service.php',
                'storage.php',
                'userInterface.php'
            ]
        ];
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->extensionLoad($configs, $container);
    }

    /**
     * @throws \Exception
     */
    public function loadContainerConfiguration(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(
                $this->getSystemPathBuilder()->namespaceToFullPath(Manager::CONTEXT) . 'Resources' .
                DIRECTORY_SEPARATOR . 'Configuration'
            )
        );

        $loader->load('configuration.yaml');
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new UserDetailsRendererCompilerPass());
        $container->addCompilerPass(new UserPictureProviderCompilerPass());
    }
}