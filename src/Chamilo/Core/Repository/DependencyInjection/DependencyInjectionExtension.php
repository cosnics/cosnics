<?php
namespace Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Core\Repository\DependencyInjection\CompilerPass\IncludeParserCompilerPass;
use Chamilo\Core\Repository\DependencyInjection\CompilerPass\PublicationAggregatorCompilerPass;
use Chamilo\Core\Repository\DependencyInjection\CompilerPass\PublicationTargetProcessorCompilerPass;
use Chamilo\Core\Repository\DependencyInjection\CompilerPass\WorkspaceExtensionCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Extension on the dependency injection container.
 * Loads local services and parameters for this package.
 *
 * @see     http://symfony.com/doc/current/components/dependency_injection/compilation.html
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Repository' => [
                'package.xml',
                'console.xml',
                'content_object.xml',
                'registration.xml',
                'services.xml',
                'tables.xml',
                'menu.xml'
            ],
            'Chamilo\Core\Repository\Feedback' => ['services.xml'],
            'Chamilo\Core\Repository\UserView' => ['tables.xml'],
            'Chamilo\Core\Repository\Workspace' => ['services.xml', 'tables.xml'],
            'Chamilo\Core\Repository\Workspace\Favourite' => ['services.xml', 'tables.xml'],
            'Chamilo\Core\Repository\Workspace\Rights' => ['tables.xml']
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PublicationAggregatorCompilerPass());
        $container->addCompilerPass(new PublicationTargetProcessorCompilerPass());
        $container->addCompilerPass(new WorkspaceExtensionCompilerPass());
        $container->addCompilerPass(new IncludeParserCompilerPass());
    }
}