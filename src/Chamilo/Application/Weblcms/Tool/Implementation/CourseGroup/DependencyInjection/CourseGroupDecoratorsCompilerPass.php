<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add CourseGroup decorator objects to the CourseGroupDecoratorsManager
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseGroupDecoratorsCompilerPass implements CompilerPassInterface
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
        if ($container->hasDefinition('chamilo.application.weblcms.tool.implementation.course_group.decorator.manager'))
        {
            $definition = $container->getDefinition(CourseGroupDecoratorsManager::class);

            $this->addFormDecorators($container, $definition);
            $this->addServiceDecorators($container, $definition);
            $this->addActionsDecorators($container, $definition);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     */
    protected function addFormDecorators(ContainerBuilder $container, Definition $definition)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'chamilo.application.weblcms.tool.implementation.course_group.decorator.form'
        );

        foreach ($taggedServices as $taggedServiceId => $tags)
        {
            $definition->addMethodCall('addFormDecorator', array(new Reference($taggedServiceId)));
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     */
    protected function addServiceDecorators(ContainerBuilder $container, Definition $definition)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'chamilo.application.weblcms.tool.implementation.course_group.decorator.service'
        );

        foreach ($taggedServices as $taggedServiceId => $tags)
        {
            $definition->addMethodCall('addServiceDecorator', array(new Reference($taggedServiceId)));
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     */
    protected function addActionsDecorators(ContainerBuilder $container, Definition $definition)
    {
        $taggedActionss = $container->findTaggedServiceIds(
            'chamilo.application.weblcms.tool.implementation.course_group.decorator.actions'
        );

        foreach ($taggedActionss as $taggedActionsId => $tags)
        {
            $definition->addMethodCall('addActionsDecorator', array(new Reference($taggedActionsId)));
        }
    }
}