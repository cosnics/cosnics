<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheAdapterCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheDataPreLoaderCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineConditionPartTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineEventListenerCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\FormTypeCompilerPass;
use Chamilo\Libraries\DependencyInjection\Configuration\LibrariesConfiguration;
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

    public function getAlias()
    {
        return 'chamilo.libraries';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Libraries' => [
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
                'storage.doctrine_orm.xml',
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
        $this->processLibrariesConfiguration($configs, $container);
    }

    protected function processLibrariesConfiguration(array $configuration, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new LibrariesConfiguration(), $configuration);

        if (array_key_exists('doctrine', $config) && array_key_exists('orm', $config['doctrine']))
        {
            $ormConfig = $config['doctrine']['orm'];

            if (array_key_exists('mappings', $ormConfig))
            {
                $mappingDriverDef = $container->getDefinition('Doctrine\ORM\MappingDriver');
                $mappingDriverDef->setArguments([$ormConfig['mappings']]);
            }

            if (array_key_exists('resolve_target_entities', $ormConfig))
            {
                $resolveTargetEntityListenerDef = $container->getDefinition(
                    'Doctrine\ORM\Tools\ResolveTargetEntityListener'
                );

                foreach ($ormConfig['resolve_target_entities'] as $name => $implementation)
                {
                    $resolveTargetEntityListenerDef->addMethodCall(
                        'addResolveTargetEntity', [$name, $implementation, []]
                    );
                }

                $resolveTargetEntityListenerDef->addTag(
                    'doctrine.orm.event_listener', ['event' => 'loadClassMetadata']
                );
            }
        }
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConsoleCompilerPass());
        $container->addCompilerPass(new CacheDataPreLoaderCompilerPass());
        $container->addCompilerPass(new CacheAdapterCompilerPass());
        $container->addCompilerPass(new DoctrineEventListenerCompilerPass());
        $container->addCompilerPass(new FormTypeCompilerPass());
        $container->addCompilerPass(new AuthenticationCompilerPass());
        $container->addCompilerPass(new DoctrineConditionPartTranslatorCompilerPass());
    }
}