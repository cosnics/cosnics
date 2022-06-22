<?php
namespace Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Core\Repository\DependencyInjection\CompilerPass\IncludeParserCompilerPass;
use Chamilo\Core\Repository\DependencyInjection\CompilerPass\PublicationAggregatorCompilerPass;
use Chamilo\Core\Repository\DependencyInjection\CompilerPass\WorkspaceExtensionCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ICompilerPassExtension
{
    /**
     * Returns the recommended alias to use in XML.
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Repository' => [
                'instance.xml',
                'console.xml',
                'content_object.xml',
                'registration.xml',
                'services.xml'
            ],
            'Chamilo\Core\Repository\Feedback' => ['services.xml'],
            'Chamilo\Core\Repository\Workspace' => ['services.xml']
        ];
    }

    /**
     * Registers the compiler passes in the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PublicationAggregatorCompilerPass());
        $container->addCompilerPass(new WorkspaceExtensionCompilerPass());
        $container->addCompilerPass(new IncludeParserCompilerPass());
    }
}