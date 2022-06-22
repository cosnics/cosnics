<?php
namespace Chamilo\Core\Group\DependencyInjection;

use Chamilo\Core\Group\DependencyInjection\CompilerPass\GroupEventListenerCompilerPass;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\Group\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ICompilerPassExtension, IConfigurableExtension
{

    public function getAlias()
    {
        return 'chamilo.core.group';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Group' => ['services.xml']];
    }

    public function loadContainerConfiguration(ContainerBuilder $container)
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()), ChamiloRequest::createFromGlobals());

        $loader = new YamlFileLoader(
            $container, new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Core\Group'))
        );

        $loader->load('Config.yml');
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GroupEventListenerCompilerPass());
    }
}