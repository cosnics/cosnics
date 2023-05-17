<?php
namespace Chamilo\Libraries\DependencyInjection\Traits;

use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

trait IConfigurableExtensionTrait
{
    /**
     * @return string[][]
     */
    abstract public function getContainerConfigurationFiles(): array;

    abstract public function getSystemPathBuilder(): SystemPathBuilder;

    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        foreach ($this->getContainerConfigurationFiles() as $context => $configurationFiles)
        {
            $loader = new YamlFileLoader(
                $container, new FileLocator($this->getSystemPathBuilder()->getConfigurationPath($context))
            );

            foreach ($configurationFiles as $configurationFile)
            {
                $loader->load($configurationFile);
            }
        }
    }
}