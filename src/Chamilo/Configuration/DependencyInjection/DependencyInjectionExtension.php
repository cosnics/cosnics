<?php
namespace Chamilo\Configuration\DependencyInjection;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @package Chamilo\Configuration\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.configuration';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Configuration' => ['configuration.xml', 'registration.xml', 'language.xml']];
    }

    public function load(array $config, ContainerBuilder $container)
    {
        parent::load($config, $container);

        $fileConfigurationLocator = new FileConfigurationLocator($this->getPathBuilder());

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
}