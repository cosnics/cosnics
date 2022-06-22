<?php

namespace Chamilo\Libraries\Authentication\SecurityToken\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\Authentication\SecurityToken\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.libraries.authentication.security_token';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Authentication\SecurityToken' => ['authentication.xml']];
    }
}