<?php

namespace Chamilo\Libraries\Authentication\Ldap\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\Authentication\Ldap\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.libraries.authentication.ldap';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Authentication\Ldap' => ['authentication.xml']];
    }
}