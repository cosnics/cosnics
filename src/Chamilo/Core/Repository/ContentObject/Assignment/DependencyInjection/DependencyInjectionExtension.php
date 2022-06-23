<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.repository.content_object.assignment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\Assignment\Display' => ['services.xml']];
    }

}