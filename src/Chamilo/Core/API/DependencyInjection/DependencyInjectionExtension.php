<?php
namespace Chamilo\Core\API\DependencyInjection;

use Chamilo\Core\API\DependencyInjection\CompilerPass\APIRoutingLoaderExtensionCompilerPass;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 *
 * @package Chamilo\Configuration\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends Extension implements ExtensionInterface, ICompilerPassExtension, IConfigurableExtension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @throws \InvalidArgumentException|\Exception When provided tag is not defined in this extension
     *         @api
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

        $loader = new XmlFileLoader(
            $container,
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Core\API') . 'DependencyInjection'));

        $loader->load('services.xml');
    }

    /**
     * Loads the configuration for this package in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(
                Path::getInstance()->namespaceToFullPath('Chamilo\Core\API') .
                'Resources/Configuration'
            )
        );

        $loader->load('Config.yml');
    }

    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *         @api
     */
    public function getAlias(): string
    {
        return 'chamilo.core.api';
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new APIRoutingLoaderExtensionCompilerPass());
    }
}