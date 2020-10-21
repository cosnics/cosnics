<?php
namespace Chamilo\Application\ExamAssignment\DependencyInjection;

use Chamilo\Application\ExamAssignment\DependencyInjection\CompilerPass\ExamAssignmentEventListenerCompilerPass;
use Chamilo\Application\ExamAssignment\DependencyInjection\CompilerPass\UserConverterCompilerPass;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
class DependencyInjectionExtension extends Extension implements ExtensionInterface
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
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Application\ExamAssignment') . 'DependencyInjection'));

//        $xmlFileLoader->load('services.xml');
    }

    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     * 
     * @return string
     */
    public function getAlias()
    {
        return 'chamilo.application.exam_assignments';
    }
}
