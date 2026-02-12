<?php
namespace Chamilo\Libraries\DependencyInjection\Architecture\Interface;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Dependency Injection Extension interface to support the possibility to add compiler passes to the container
 * Interface ICompilerPassExtension
 *
 * @package Chamilo\Libraries\DependencyInjection\Architecture\Interface
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ICompilerPassExtension extends ExtensionInterface
{
    public function registerCompilerPasses(ContainerBuilder $container): void;
}