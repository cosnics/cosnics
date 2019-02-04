<?php
namespace Chamilo\Application\Plagiarism\DependencyInjection;

use Chamilo\Application\Plagiarism\DependencyInjection\CompilerPass\PlagiarismEventListenerCompilerPass;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
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
     * @param array $configuration
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     *
     */
    public function load(array $configuration, ContainerBuilder $container)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));
        
        $xmlFileLoader = new XmlFileLoader(
            $container, 
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Application\Plagiarism') . 'DependencyInjection'));

        $xmlFileLoader->load('services.xml');
        $xmlFileLoader->load('turnitin.xml');
    }

    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     * 
     * @return string
     */
    public function getAlias()
    {
        return 'chamilo.application.plagiarism';
    }

    /**
     * Registers the compiler passes in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PlagiarismEventListenerCompilerPass());
    }
}