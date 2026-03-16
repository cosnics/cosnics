<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\UserInterface\Form\Factory\FormFactoryBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(FormFactoryBuilder::class)) {
            $taggedServices = $container->findTaggedServiceIds(FormTypeInterface::class);
            $definition = $container->getDefinition(FormFactoryBuilder::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addAdditionalFormType', [new Reference($taggedServiceId)]);
            }
        }
    }
}