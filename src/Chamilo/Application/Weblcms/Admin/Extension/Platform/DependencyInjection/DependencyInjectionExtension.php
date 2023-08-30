<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Admin\Extension\Platform\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.admin.extension.platform';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Admin\Extension\Platform' => ['package.xml', 'tables.xml']];
    }
}