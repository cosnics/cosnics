<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Application\Calendar\Extension\Personal\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Application\Calendar\Extension\Personal\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.repository.contentobject.task.integration.chamilo.application.calendar.extension.personal';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Application\Calendar\Extension\Personal' => ['package.xml']];
    }
}