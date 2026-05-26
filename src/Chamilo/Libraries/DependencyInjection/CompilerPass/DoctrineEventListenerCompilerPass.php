<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Storage\Factory\DoctrineEntityManagerFactory;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add doctrine event listeners to the doctrine entity manager factory
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineEventListenerCompilerPass implements CompilerPassInterface
{
    public const string TAG_EVENT_LISTENER = 'Doctrine\Common\EventListener';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(DoctrineEntityManagerFactory::class)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(self::TAG_EVENT_LISTENER);
        $doctrineEntityManagerFactoryDef = $container->getDefinition(DoctrineEntityManagerFactory::class);

        foreach ($taggedServices as $taggedServiceId => $tags) {
            $events = is_subclass_of($taggedServiceId, EventSubscriber::class) ? [] : $tags[0]['event'];

            $doctrineEntityManagerFactoryDef->addMethodCall(
                'addEventListener', [$events, new Reference($taggedServiceId)]
            );
        }
    }
}