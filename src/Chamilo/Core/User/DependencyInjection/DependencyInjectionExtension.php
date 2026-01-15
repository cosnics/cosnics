<?php
namespace Chamilo\Core\User\DependencyInjection;

use Chamilo\Core\User\DependencyInjection\CompilerPass\UserDetailsRendererCompilerPass;
use Chamilo\Core\User\DependencyInjection\CompilerPass\UserPictureProviderCompilerPass;
use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\User\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.user';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\User' => [
                'architecture.domain.xml',
                'architecture.eventDispatcher.xml',
                'implementation.admin.xml',
                'implementation.home.xml',
                'implementation.menu.xml',
                'implementation.user.xml',
                'service.xml',
                'storage.xml',
                'userInterface.tables.xml'
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new UserDetailsRendererCompilerPass());
        $container->addCompilerPass(new UserPictureProviderCompilerPass());
    }
}