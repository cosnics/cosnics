<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.integration.chamilo.core.reporting';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting' => ['package.xml']];
    }
}