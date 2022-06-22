<?php
namespace Chamilo\Core\User\Roles\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\User\Roles\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.core.user.roles';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\User\Roles' => ['repository.xml', 'services.xml']];
    }
}