<?php
namespace Chamilo\Core\Group\DependencyInjection;

use Chamilo\Core\Group\DependencyInjection\CompilerPass\GroupEventListenerCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Chamilo\Libraries\DependencyInjection\Traits\IConfigurableExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Group\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension, IConfigurableExtension
{
    use ExtensionTrait;
    use IConfigurableExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.group';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Group' => ['services.xml', 'tables.xml']];
    }

    public function getContainerConfigurationFiles(): array
    {
        return ['Chamilo\Core\Group' => ['Config.yml']];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GroupEventListenerCompilerPass());
    }
}