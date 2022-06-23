<?php

namespace Chamilo\Core\Home\Integration\Chamilo\Core\Repository\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.home.integration.chamilo.core.repository';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Home\Integration\Chamilo\Core\Repository' => ['services.xml']];
    }
}