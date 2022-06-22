<?php
namespace Chamilo\Core\Notification\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\Path;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\Notification\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements IConfigurableExtension
{
    public function getAlias()
    {
        return 'chamilo.core.notification';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Notification' => ['services.xml']];
    }

    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath('Chamilo\Core\Notification') . 'Resources/Configuration'
            )
        );

        $loader->load('Config.yml');
    }
}