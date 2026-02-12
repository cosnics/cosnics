<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Mail\Factory\MailerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class MailerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(MailerFactory::class)) {
            $taggedServices = $container->findTaggedServiceIds(MailerInterface::class);
            $definition = $container->getDefinition(MailerFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addMailer', [new Reference($taggedServiceId)]);
            }
        }
    }
}