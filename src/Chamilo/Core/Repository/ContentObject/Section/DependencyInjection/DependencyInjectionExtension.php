<?php
namespace Chamilo\Core\Repository\ContentObject\Section\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Section\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.repository.contentobject.section';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\Section' => ['package.xml']];
    }
}