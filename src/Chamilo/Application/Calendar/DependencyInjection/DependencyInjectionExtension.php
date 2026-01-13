<?php
namespace Chamilo\Application\Calendar\DependencyInjection;

use Chamilo\Application\Calendar\DependencyInjection\CompilerPass\CalendarProviderCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Calendar\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.application.calendar';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Calendar' => ['services.xml']];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CalendarProviderCompilerPass());
    }
}