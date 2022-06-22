<?php

namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.application.calendar.extension.personal.integration.chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository' => ['services.xml']];
    }
}