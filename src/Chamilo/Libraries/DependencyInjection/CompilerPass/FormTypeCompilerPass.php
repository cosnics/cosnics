<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add new FormTypes as services to symfony forms
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FormTypeCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('chamilo.libraries.format.form.form_factory_builder'))
        {
            $taggedServices = $container->findTaggedServiceIds('form.type');

            $consoleDefinition = $container->getDefinition(
                'chamilo.libraries.format.form.form_factory_builder');

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $consoleDefinition->addMethodCall('addFormType', array(new Reference($taggedServiceId)));
            }
        }
    }
}