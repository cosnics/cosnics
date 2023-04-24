<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add new FormTypes as services to symfony forms
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AuthenticationCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(AuthenticationValidator::class))
        {
            $taggedServices = $container->findTaggedServiceIds(AuthenticationInterface::class);
            $definition = $container->getDefinition(AuthenticationValidator::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addAuthentication', [new Reference($taggedServiceId)]);
            }
        }
    }
}