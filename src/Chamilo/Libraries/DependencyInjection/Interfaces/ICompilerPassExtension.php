<?php
namespace Chamilo\Libraries\DependencyInjection\Interfaces;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Dependency Injection Extension interface to support the possibility to add compiler passes to the container
 * Interface ICompilerPassExtension
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ICompilerPassExtension extends ExtensionInterface
{

    /**
     * Registers the compiler passes in the container
     *
     * @param ContainerBuilder $container
     */
    public function registerCompilerPasses(ContainerBuilder $container);
}