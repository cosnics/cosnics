<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.repository.contentobject.link.integration.chamilo.core.repository.contentobject.assignment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Repository\ContentObject\Assignment' => ['package.xml']];
    }
}