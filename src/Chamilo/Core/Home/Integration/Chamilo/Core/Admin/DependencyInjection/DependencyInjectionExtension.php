<?php

namespace Chamilo\Core\Home\Integration\Chamilo\Core\Admin\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Admin\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.home.integration.chamilo.core.admin';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Home\Integration\Chamilo\Core\Admin' => ['services.xml']];
    }
}