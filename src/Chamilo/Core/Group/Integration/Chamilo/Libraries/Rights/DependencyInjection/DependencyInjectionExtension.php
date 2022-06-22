<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.core.group.integration.chamilo.libraries.rights';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights' => ['services.xml']];
    }
}