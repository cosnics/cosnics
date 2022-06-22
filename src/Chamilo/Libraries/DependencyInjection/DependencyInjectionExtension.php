<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\Console\Command\Vendor\PHPStan\PHPStanPackages;
use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheServicesConstructorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineEventListenerCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\FormTypeCompilerPass;
use Chamilo\Libraries\DependencyInjection\Configuration\LibrariesConfiguration;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ICompilerPassExtension
{

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
                'platform.xml',
                'storage.xml',
                'storage.adodb.xml',
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

    public function load(array $configuration, ContainerBuilder $container)
    {
        parent::load($configuration, $container);

        $this->processLibrariesConfiguration($configuration, $container);
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
                $mappingDriverDef->setArguments(array($ormConfig['mappings']));
            }

            if (array_key_exists('resolve_target_entities', $ormConfig))
            {
                $resolveTargetEntityListenerDef = $container->getDefinition(
                    'Doctrine\ORM\Tools\ResolveTargetEntityListener'
                );

                foreach ($ormConfig['resolve_target_entities'] as $name => $implementation)
                {
                    $resolveTargetEntityListenerDef->addMethodCall(
                        'addResolveTargetEntity', array($name, $implementation, [])
                    );
                }

                $resolveTargetEntityListenerDef->addTag(
                    'doctrine.orm.event_listener', array('event' => 'loadClassMetadata')
                );
            }
        }

        $this->processPHPStanConfig($config, $container);
    }

    protected function processPHPStanConfig(array $configuration, ContainerBuilder $container)
    {
        if (!array_key_exists('phpstan', $configuration))
        {
            return;
        }

        $packagesConfiguration = $configuration['phpstan']['packages'];

        if ($container->hasDefinition(PHPStanPackages::class))
        {
            $phpStanPackagesDefinition = $container->getDefinition(PHPStanPackages::class);
            $phpStanPackagesDefinition->addMethodCall('setPackagesFromConfiguration', array($packagesConfiguration));
        }
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConsoleCompilerPass());
        $container->addCompilerPass(new CacheServicesConstructorCompilerPass());
        $container->addCompilerPass(new DoctrineEventListenerCompilerPass());
        $container->addCompilerPass(new FormTypeCompilerPass());
        $container->addCompilerPass(new AuthenticationCompilerPass());
    }
}