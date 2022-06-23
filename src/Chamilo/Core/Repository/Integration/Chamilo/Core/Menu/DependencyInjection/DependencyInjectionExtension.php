<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.repository.integration.chamilo.core.menu';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\Integration\Chamilo\Core\Menu' => ['services.xml']];
    }
}