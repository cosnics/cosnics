<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.application.weblcms.integration.chamilo.core.tracking';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking' => ['services.xml']];
    }
}