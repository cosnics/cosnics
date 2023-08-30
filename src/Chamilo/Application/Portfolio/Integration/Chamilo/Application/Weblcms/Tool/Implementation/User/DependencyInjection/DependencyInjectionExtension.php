<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Application\Weblcms\Tool\Implementation\User\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Application\Weblcms\Tool\Implementation\User\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.portfolio.integration.chamilo.application.weblcms.tool.implementation.user';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Portfolio\Integration\Chamilo\Application\Weblcms\Tool\Implementation\User' => ['package.xml']];
    }
}