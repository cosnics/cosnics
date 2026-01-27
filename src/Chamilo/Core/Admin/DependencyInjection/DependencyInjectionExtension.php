<?php
namespace Chamilo\Core\Admin\DependencyInjection;

use Chamilo\Core\Admin\DependencyInjection\CompilerPass\ActionProviderCompilerPass;
use Chamilo\Core\Admin\DependencyInjection\CompilerPass\SettingsConnectorsCompilerPass;
use Chamilo\Core\Admin\Service\FileConfigurationLocator;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @package Chamilo\Core\Admin\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait
    {
        load as public extensionLoad;
    }

    public function getAlias(): string
    {
        return 'chamilo.core.admin';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Admin' => [
                'architecture.domain.xml',
                'implementation.admin.xml',
                'implementation.home.xml',
                'service.xml',
                'service.consulter.xml',
                'service.dataLoader.xml',
                'service.finder.xml',
                'storage.xml',
                'userInterface.table.xml'
            ]
        ];
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->extensionLoad($configs, $container);

        $fileConfigurationLocator = new FileConfigurationLocator($this->getSystemPathBuilder());

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

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ActionProviderCompilerPass());
        $container->addCompilerPass(new SettingsConnectorsCompilerPass());
    }
}