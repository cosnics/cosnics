<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.application.weblcms.integration.chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository' => ['services.xml']];
    }
}