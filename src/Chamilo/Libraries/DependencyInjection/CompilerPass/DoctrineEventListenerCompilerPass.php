<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

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

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.orm.entity_manager_factory'))
        {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds('doctrine.orm.event_listener');
        $doctrineEntityManagerFactoryDef = $container->getDefinition('Doctrine\ORM\EntityManagerFactory');
        $doctrineTestEntityManagerFactoryDef = $container->getDefinition('Doctrine\ORM\Test\EntityManagerFactory');

        foreach ($taggedServices as $taggedServiceId => $tags)
        {
            $doctrineEntityManagerFactoryDef->addMethodCall(
                'addEventListener', array($tags[0]['event'], new Reference($taggedServiceId))
            );

            $doctrineTestEntityManagerFactoryDef->addMethodCall(
                'addEventListener', array($tags[0]['event'], new Reference($taggedServiceId))
            );
        }
    }
}