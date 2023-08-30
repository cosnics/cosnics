<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\ContentObject\Portfolio\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\ContentObject\Portfolio\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.portfolio.integration.chamilo.core.repository.contentobject.portfolio';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\ContentObject\Portfolio' => ['package.xml']];
    }
}