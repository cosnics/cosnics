<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.core.user.integration.chamilo.core.menu';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\User\Integration\Chamilo\Core\Menu' => ['services.xml']];
    }
}