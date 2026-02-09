<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ButtonRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheAdapterCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheDataPreLoaderCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConditionTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConditionVariableTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\EventDispatcherCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\HashingCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\MailerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait {
        load as public extentensionLoad;
    }

    public function getAlias(): string
    {
        return 'chamilo.libraries';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Libraries' => [
                'architecture.php',
                'calendar.php',
                'filesystem.php',
                'protocol.authentication.php',
                'protocol.console.php',
                'protocol.mail.php',
                'protocol.microsoft.php',
                'protocol.security.php',
                'protocol.session.php',
                'service.php',
                'storage.php',
                'userInterface.breadcrumb.php',
                'userInterface.buttonToolBar.php',
                'userInterface.form.php',
                'userInterface.layout.php',
                'userInterface.notificationMessage.php',
                'userInterface.tab.php',
                'userInterface.table.php',
                'userInterface.theme.php',
                'userInterface.translation.php',
                'userInterface.tree.php',
                'vendor.php'
            ]
        ];
    }

    public function getContext(): string
    {
        return 'Chamilo\Libraries';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->extentensionLoad($configs, $container);
    }

    public function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConsoleCompilerPass());
        $container->addCompilerPass(new HashingCompilerPass());
        $container->addCompilerPass(new MailerCompilerPass());
        $container->addCompilerPass(new CacheDataPreLoaderCompilerPass());
        $container->addCompilerPass(new CacheAdapterCompilerPass());
        $container->addCompilerPass(new AuthenticationCompilerPass());
        $container->addCompilerPass(new ConditionTranslatorCompilerPass());
        $container->addCompilerPass(new ConditionVariableTranslatorCompilerPass());
        $container->addCompilerPass(new EventDispatcherCompilerPass());
        $container->addCompilerPass(new ButtonRendererCompilerPass());
    }
}