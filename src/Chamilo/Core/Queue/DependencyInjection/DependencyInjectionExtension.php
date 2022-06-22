<?php
namespace Chamilo\Core\Queue\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\Path;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\Queue\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements IConfigurableExtension
{

    public function getAlias()
    {
        return 'chamilo.core.queue';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Queue' => ['services.xml']];
    }

    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath('Chamilo\Core\Queue') . 'Resources/Configuration'
            )
        );

        $loader->load('Config.yml');
    }
}