<?php

namespace Chamilo\Libraries\Authentication\Platform\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\Authentication\Platform\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.libraries.authentication.platform';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Authentication\Platform' => ['authentication.xml']];
    }
}