<?php
namespace Chamilo\Core\Metadata\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Metadata\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.core.metadata';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Metadata' => ['services.xml']];
    }
}