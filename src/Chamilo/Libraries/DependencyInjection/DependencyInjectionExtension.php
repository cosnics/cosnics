<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheAdapterCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheDataPreLoaderCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineConditionPartTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\EventDispatcherCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\FormTypeCompilerPass;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait
    {
        load as public extentensionLoad;
    }

    public function getAlias(): string
    {
        return 'chamilo.libraries';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Libraries' => [
                'package.xml',
                'architecture.xml',
                'authentication.xml',
                'cache.xml',
                'file.xml',
                'format.xml',
                'hashing.xml',
                'mail.xml',
                'platform.xml',
                'storage.xml',
                'storage.doctrine.xml',
                'storage.doctrine_test.xml',
                'support.xml',
                'translation.xml',
                'utilities.xml',
                'vendor.xml',
                'console.xml',
                'console.doctrine.xml'
            ]
        ];
    }

    public function getContext(): string
    {
        return 'Chamilo\Libraries';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->extentensionLoad($configs, $container);
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConsoleCompilerPass());
        $container->addCompilerPass(new CacheDataPreLoaderCompilerPass());
        $container->addCompilerPass(new CacheAdapterCompilerPass());
        $container->addCompilerPass(new FormTypeCompilerPass());
        $container->addCompilerPass(new AuthenticationCompilerPass());
        $container->addCompilerPass(new DoctrineConditionPartTranslatorCompilerPass());
        $container->addCompilerPass(new EventDispatcherCompilerPass());
    }
}