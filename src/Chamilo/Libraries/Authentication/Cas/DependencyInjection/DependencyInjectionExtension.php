<?php

namespace Chamilo\Libraries\Authentication\Cas\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\Authentication\Cas\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.libraries.authentication.cas';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Authentication\Cas' => ['authentication.xml']];
    }
}