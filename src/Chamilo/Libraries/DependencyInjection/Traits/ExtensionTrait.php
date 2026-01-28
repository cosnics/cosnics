<?php
namespace Chamilo\Libraries\DependencyInjection\Traits;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

trait ExtensionTrait
{
    /**
     * @return string[][]
     */
    abstract public function getConfigurationFiles(): array;

    abstract public function getSystemPathBuilder(): SystemPathBuilder;

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        foreach ($this->getConfigurationFiles() as $context => $configurationFiles)
        {
            $loader = new PhpFileLoader(
                $container,
                new FileLocator($this->getSystemPathBuilder()->getConfigurationPath($context) . 'DependencyInjection')
            );

            foreach ($configurationFiles as $configurationFile)
            {
                $loader->load($configurationFile);
            }
        }
    }
}