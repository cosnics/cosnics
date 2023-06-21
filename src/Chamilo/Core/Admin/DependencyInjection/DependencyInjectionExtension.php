<?php
namespace Chamilo\Core\Admin\DependencyInjection;

use Chamilo\Core\Admin\DependencyInjection\CompilerPass\ActionProviderCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Admin\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.admin';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Admin\Announcement' => ['publication.xml', 'services.xml', 'tables.xml'],
            'Chamilo\Core\Admin' => ['services.xml', 'tables.xml']
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ActionProviderCompilerPass());
    }
}