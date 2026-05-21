<?php
namespace Chamilo\Core\Group\DependencyInjection;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\IConfigurableExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\Group\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, IConfigurableExtension
{
    use ExtensionTrait {
        load as public extensionLoad;
    }

    public function getAlias(): string
    {
        return 'chamilo.core.group';
    }

    public function getConfigurationFiles(): array
    {
        return [
            Manager::CONTEXT => [
                'application.php',
                'architecture.php',
                'implementation.admin.php',
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
}