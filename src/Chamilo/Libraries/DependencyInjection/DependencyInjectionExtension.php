<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheServicesConstructorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineEventListenerCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\FormTypeCompilerPass;
use Chamilo\Libraries\DependencyInjection\Configuration\LibrariesConfiguration;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Extension on the dependency injection container.
 * Loads local services and parameters for this package.
 *
 * @see http://symfony.com/doc/current/components/dependency_injection/compilation.html
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends Extension implements ExtensionInterface, ICompilerPassExtension
{

    /**
     * Loads a specific configuration.
     *
     * @param string[] $configuration
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configuration, ContainerBuilder $container)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

        $xmlFileLoader = new XmlFileLoader(
            $container,
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Libraries') . 'DependencyInjection'));

        $xmlFileLoader->load('architecture.xml');
        $xmlFileLoader->load('authentication.xml');
        $xmlFileLoader->load('cache.xml');
        $xmlFileLoader->load('file.xml');
        $xmlFileLoader->load('format.xml');
        $xmlFileLoader->load('hashing.xml');
        $xmlFileLoader->load('platform.xml');
        $xmlFileLoader->load('storage.xml');
        $xmlFileLoader->load('storage.adodb.xml');
        $xmlFileLoader->load('storage.doctrine.xml');
        $xmlFileLoader->load('storage.doctrine_test.xml');
        $xmlFileLoader->load('storage.doctrine_orm.xml');
        $xmlFileLoader->load('translation.xml');
        $xmlFileLoader->load('utilities.xml');
        $xmlFileLoader->load('vendor.xml');

        // Console configuration
        $xmlFileLoader->load('console.xml');
        $xmlFileLoader->load('console.doctrine.xml');

        $this->processLibrariesConfiguration($configuration, $container);
    }

    /**
     * Registers the compiler passes in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConsoleCompilerPass());
        $container->addCompilerPass(new CacheServicesConstructorCompilerPass());
        $container->addCompilerPass(new DoctrineEventListenerCompilerPass());
        $container->addCompilerPass(new FormTypeCompilerPass());
        $container->addCompilerPass(new AuthenticationCompilerPass());
    }

    /**
     * Processes the configuration for chamilo.libraries
     *
     * @param string[] $configuration
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
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
                    'Doctrine\ORM\Tools\ResolveTargetEntityListener');

                foreach ($ormConfig['resolve_target_entities'] as $name => $implementation)
                {
                    $resolveTargetEntityListenerDef->addMethodCall(
                        'addResolveTargetEntity',
                        array($name, $implementation, array()));
                }

                $resolveTargetEntityListenerDef->addTag(
                    'doctrine.orm.event_listener',
                    array('event' => 'loadClassMetadata'));
            }
        }

        $this->processPHPStanConfig($config, $container);
    }


    protected function processPHPStanConfig(array $configuration, ContainerBuilder $container)
    {
        if(!array_key_exists('phpstan', $configuration))
        {
            return;
        }
    }

    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'chamilo.libraries';
    }
}