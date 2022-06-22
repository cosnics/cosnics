<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractDependencyInjectionExtension extends Extension implements ExtensionInterface
{
    /**
     * @return string[][]
     */
    abstract public function getConfigurationFiles(): array;

    public function getPathBuilder(): PathBuilder
    {
        return new PathBuilder(new ClassnameUtilities(new StringUtilities()));
    }

    public function load(array $config, ContainerBuilder $container)
    {
        foreach ($this->getConfigurationFiles() as $context => $configurationFiles)
        {
            $loader = new XmlFileLoader(
                $container,
                new FileLocator($this->getPathBuilder()->getConfigurationPath($context) . 'DependencyInjection')
            );

            foreach ($configurationFiles as $configurationFile)
            {
                $loader->load($configurationFile);
            }
        }
    }
}