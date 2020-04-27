<?php

namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

use Chamilo\Core\Repository\Service\IncludeParserManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class IncludeParserCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(IncludeParserManager::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Core\Repository\Service\IncludeParser\ChamiloIncludeParser'
            );

            $definition = $container->getDefinition(IncludeParserManager::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addParser', array(new Reference($taggedServiceId))
                );
            }
        }
    }
}
