<?php
namespace Chamilo\Core\Rights\Structure\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Rights\Structure\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.core.rights.structure';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Rights\Structure' => ['repository.xml', 'services.xml', 'console.xml']];
    }
}