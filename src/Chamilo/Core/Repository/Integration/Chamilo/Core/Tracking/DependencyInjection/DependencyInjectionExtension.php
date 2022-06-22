<?php

namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.core.repository.integration.chamilo.core.tracking';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking' => ['services.xml']];
    }
}