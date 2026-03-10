<?php
namespace Chamilo\Application\Calendar\DependencyInjection;

use Chamilo\Application\Calendar\DependencyInjection\CompilerPass\CalendarExtensionActionProviderCompilerPass;
use Chamilo\Application\Calendar\DependencyInjection\CompilerPass\CalendarExtensionDataProviderCompilerPass;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
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
        return [
            Manager::CONTEXT => [
                'application.php',
                'architecture.domain.php',
                'implementation.home.php',
                'service.php',
                'storage.php'
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CalendarExtensionActionProviderCompilerPass());
        $container->addCompilerPass(new CalendarExtensionDataProviderCompilerPass());
    }
}