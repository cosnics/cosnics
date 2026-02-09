<?php
namespace Chamilo\Core\User\DependencyInjection;

use Chamilo\Core\User\DependencyInjection\CompilerPass\UserDetailsRendererCompilerPass;
use Chamilo\Core\User\DependencyInjection\CompilerPass\UserPictureProviderCompilerPass;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
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
                'architecture.php',
                'implementation.admin.php',
                'implementation.home.php',
                'implementation.menu.php',
                'implementation.user.php',
                'service.php',
                'storage.php',
                'userInterface.php'
            ]
        ];
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new UserDetailsRendererCompilerPass());
        $container->addCompilerPass(new UserPictureProviderCompilerPass());
    }
}