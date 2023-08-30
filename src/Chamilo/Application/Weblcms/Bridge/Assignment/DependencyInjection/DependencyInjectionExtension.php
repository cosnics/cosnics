<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.bridge.assignment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Bridge\Assignment' => ['package.xml', 'services.xml']];
    }
}