<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.application.weblcms.tool.implementation.learningpath.integration.chamilo.core.repository.contentobject.learningpath';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath' => ['package.xml']];
    }
}