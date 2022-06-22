<?php
namespace Chamilo\Core\Install\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @package Chamilo\Core\Install\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ICompilerPassExtension
{
    public function getAlias()
    {
        return 'chamilo.core.install';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Install' => ['architecture.xml']];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConsoleCompilerPass());
    }
}