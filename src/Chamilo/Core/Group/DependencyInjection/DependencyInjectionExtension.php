<?php
namespace Chamilo\Core\Group\DependencyInjection;

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
class DependencyInjectionExtension extends Extension implements ExtensionInterface,
    IConfigurableExtension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *         @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

        $loader = new XmlFileLoader(
            $container,
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Core\Group') . 'DependencyInjection'));

        $loader->load('repository.xml');
        $loader->load('services.xml');
    }

    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *         @api
     */
    public function getAlias()
    {
        return 'chamilo.core.group';
    }

    /**
     * Loads the configuration for this package in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

        $loader = new YamlFileLoader(
            $container,
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Core\Group'))
        );

        $loader->load('Config.yml');
    }
}