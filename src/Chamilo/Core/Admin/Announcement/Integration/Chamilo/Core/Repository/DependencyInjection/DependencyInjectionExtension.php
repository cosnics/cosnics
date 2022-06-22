<?php

namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.core.admin.announcement.integration.chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository' => ['services.xml']];
    }

}