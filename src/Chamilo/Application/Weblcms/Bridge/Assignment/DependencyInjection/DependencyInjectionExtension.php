<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.application.weblcms.bridge.assignment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Bridge\Assignment' => ['services.xml']];
    }
}