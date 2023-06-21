<?php
namespace Chamilo\Application\Weblcms\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms' => ['publication.xml', 'services.xml']];
    }
}