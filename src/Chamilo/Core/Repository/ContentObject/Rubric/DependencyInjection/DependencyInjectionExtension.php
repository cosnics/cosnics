<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\Path;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Extension on the dependency injection container. Loads local services and parameters for this package.
 *
 * @see http://symfony.com/doc/current/components/dependency_injection/compilation.html
 *
 * @package application\hogeschool_gent
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends Extension implements IConfigurableExtension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     * @throws \Exception
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\Rubric') .
                'Resources/Configuration/DependencyInjection'
            )
        );

        $loader->load('services.xml');
        $loader->load('repositories.xml');
    }

    /**
     * Loads the configuration for this package in the container
     *
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\Rubric') .
                'Resources/Configuration'
            )
        );

        $loader->load('Config.yml');
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'chamilo.core.repository.content_object.rubric';
    }
}
