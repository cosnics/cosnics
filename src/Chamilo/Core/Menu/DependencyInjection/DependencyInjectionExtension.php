<?php
namespace Chamilo\Core\Menu\DependencyInjection;

use Chamilo\Core\Menu\DependencyInjection\CompilerPass\AvailableItemRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Menu\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.menu';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Menu' => ['services.xml', 'tables.xml']];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AvailableItemRendererCompilerPass());
    }
}