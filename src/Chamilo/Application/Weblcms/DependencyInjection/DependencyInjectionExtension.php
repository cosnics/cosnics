<?php

namespace Chamilo\Application\Weblcms\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Application\Weblcms\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.application.weblcms';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms' => ['services.xml']];
    }
}