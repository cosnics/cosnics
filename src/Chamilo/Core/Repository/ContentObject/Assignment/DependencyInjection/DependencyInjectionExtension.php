<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.core.repository.content_object.assignment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\Assignment\Display' => ['services.xml']];
    }

}