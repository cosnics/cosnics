<?php
namespace Chamilo\Core\Home\DependencyInjection;

use Chamilo\Core\Home\DependencyInjection\CompilerPass\AngularConnectorCompilerPass;
use Chamilo\Core\Home\DependencyInjection\CompilerPass\AvailableBlockRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Admin\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.home';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Home' => ['package.xml', 'publication.xml','services.xml', 'tables.xml']];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AvailableBlockRendererCompilerPass());
        $container->addCompilerPass(new AngularConnectorCompilerPass());
    }
}