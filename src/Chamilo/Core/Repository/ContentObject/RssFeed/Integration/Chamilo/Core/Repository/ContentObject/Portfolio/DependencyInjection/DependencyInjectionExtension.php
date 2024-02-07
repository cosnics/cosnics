<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Repository\ContentObject\Portfolio\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Repository\ContentObject\Portfolio\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.repository.contentobject.rssfeed.integration.chamilo.core.repository.contentobject.portfolio';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Repository\ContentObject\Portfolio' => ['package.xml']];
    }
}