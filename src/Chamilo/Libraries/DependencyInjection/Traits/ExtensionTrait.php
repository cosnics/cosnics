<?php
namespace Chamilo\Libraries\DependencyInjection\Traits;

use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

trait ExtensionTrait
{
    /**
     * @return string[][]
     */
    abstract public function getConfigurationFiles(): array;

    abstract public function getPathBuilder(): SystemPathBuilder;

    public function load(array $configs, ContainerBuilder $container)
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