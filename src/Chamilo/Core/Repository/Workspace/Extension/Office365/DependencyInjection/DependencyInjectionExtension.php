<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.core.repository.workspace.extension.office365';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\Workspace\Extension\Office365' => ['services.xml']];
    }

}