<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ApplicationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ButtonRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheAdapterCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheDataPreLoaderCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConditionTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConditionVariableTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\EventDispatcherCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\HashingCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\HtmlCalendarRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\MailerCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TabRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\UserExceptionRendererCompilerPass;
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
                'application.php',
                'architecture.php',
                'calendar.php',
                'filesystem.php',
                'implementation.admin.php',
                'protocol.authentication.php',
                'protocol.console.php',
                'protocol.errorHandling.php',
                'protocol.exceptionHandling.php',
                'protocol.mail.php',
                'protocol.microsoft.php',
                'protocol.security.php',
                'protocol.session.php',
                'service.php',
                'storage.php',
                'userInterface.alert.php',
                'userInterface.breadcrumb.php',
                'userInterface.buttonToolBar.php',
                'userInterface.form.php',
                'userInterface.layout.php',
                'userInterface.tab.php',
                'userInterface.table.php',
                'userInterface.theme.php',
                'userInterface.translation.php',
                'userInterface.tree.php'
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
        $container->addCompilerPass(new ApplicationCompilerPass());
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
        $container->addCompilerPass(new TabRendererCompilerPass());
        $container->addCompilerPass(new HtmlCalendarRendererCompilerPass());
        $container->addCompilerPass(new UserExceptionRendererCompilerPass());
    }
}
