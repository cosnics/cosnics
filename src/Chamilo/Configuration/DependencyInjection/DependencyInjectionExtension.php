<?php
namespace Chamilo\Configuration\DependencyInjection;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;

/**
 *
 * @package Chamilo\Configuration\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends Extension implements ExtensionInterface
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

        $xmlFileLoader = new XmlFileLoader(
            $container,
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Configuration') . 'DependencyInjection'));

        $xmlFileLoader->load('configuration.xml');
        $xmlFileLoader->load('registration.xml');
        $xmlFileLoader->load('language.xml');
        $xmlFileLoader->load('package.xml');

        $fileConfigurationLocator = new FileConfigurationLocator($pathBuilder);

        if ($fileConfigurationLocator->isAvailable())
        {
            $configurationFilePath = $fileConfigurationLocator->getFilePath();
            $configurationFileName = $fileConfigurationLocator->getFileName();
        }
        else
        {
            $configurationFilePath = $fileConfigurationLocator->getDefaultFilePath();
            $configurationFileName = $fileConfigurationLocator->getDefaultFileName();
        }

        $configurationXmlFileLoader = new XmlFileLoader($container, new FileLocator($configurationFilePath));
        $configurationXmlFileLoader->load($configurationFileName);
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
        return 'chamilo.configuration';
    }
}
