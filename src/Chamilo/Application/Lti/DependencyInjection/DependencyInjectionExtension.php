<?php

namespace Chamilo\Application\Lti\DependencyInjection;

use Chamilo\Application\Lti\DependencyInjection\CompilerPass\IntegrationCompilerPass;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\Path;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 *
 * @package Chamilo\Application\Calendar\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends Extension
    implements ExtensionInterface, ICompilerPassExtension, IConfigurableExtension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \Exception
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(
                Path::getInstance()->getConfigurationPath('Chamilo\Application\Lti') . 'DependencyInjection'
            )
        );

        $loader->load('services.xml');
    }

    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return
     *
     */
    public function getAlias()
    {
        return 'chamilo.application.lti';
    }

    /**
     * Registers the compiler passes in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new IntegrationCompilerPass());
    }

    /**
     * Loads the configuration for this package in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(Path::getInstance()->getConfigurationPath('Chamilo\Application\Lti'))
        );

        $loader->load('Config.yml');
    }
}