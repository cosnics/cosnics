<?php

namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.application.portfolio.integration.chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository' => ['services.xml']];
    }
}