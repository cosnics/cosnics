<?php

namespace Chamilo\Application\Weblcms\DependencyInjection;

use Chamilo\Libraries\File\Path;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Extension on the dependency injection container. Loads local services and parameters for this package.
 *
 * @see http://symfony.com/doc/current/components/dependency_injection/compilation.html
 *
 * @package application\weblcms
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends Extension implements ExtensionInterface
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath('Chamilo\Application\Weblcms') .
                'Resources/Configuration/DependencyInjection'
            )
        );

        $loader->load('services.xml');
        $loader->load('console.xml');
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
        return 'chamilo.application.weblcms';
    }
}