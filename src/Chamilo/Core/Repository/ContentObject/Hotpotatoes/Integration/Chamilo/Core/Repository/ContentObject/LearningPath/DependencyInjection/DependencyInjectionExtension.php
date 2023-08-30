<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Hotpotatoes\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.repository.contentobject.hotpotatoes.integration.chamilo.core.repository.contentobject.learningpath';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\Hotpotatoes\Integration\Chamilo\Core\Repository\ContentObject\LearningPath' => ['package.xml']];
    }
}