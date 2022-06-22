<?php

namespace Chamilo\Libraries\Authentication\Anonymous\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\Authentication\Anonymous\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.libraries.authentication.anonymous';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Authentication\Anonymous' => ['authentication.xml']];
    }
}