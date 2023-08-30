<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.integration.chamilo.core.tracking';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking' => ['package.xml', 'services.xml']];
    }
}