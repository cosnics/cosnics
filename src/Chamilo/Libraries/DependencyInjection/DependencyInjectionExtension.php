<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\CompilerPass\AuthenticationCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ButtonRendererCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheAdapterCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\CacheDataPreLoaderCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\ConsoleCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineConditionPartTranslatorCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\EventDispatcherCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\HashingCompilerPass;
use Chamilo\Libraries\DependencyInjection\CompilerPass\MailerCompilerPass;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
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
    use ExtensionTrait
    {
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
                'authentication.php',
                'cache.php',
                'calendar.php',
                'file.php',
                'format.php',
                'hashing.php',
                'mail.php',
                'platform.php',
                'protocol.microsoft.php',
                'storage.php',
                'storage.doctrine.php',
                'support.php',
                'translation.php',
                'utilities.php',
                'vendor.php',
                'console.php',
                'console.doctrine.php'
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
        $container->addCompilerPass(new DoctrineConditionPartTranslatorCompilerPass());
        $container->addCompilerPass(new EventDispatcherCompilerPass());
        $container->addCompilerPass(new ButtonRendererCompilerPass());
    }
}