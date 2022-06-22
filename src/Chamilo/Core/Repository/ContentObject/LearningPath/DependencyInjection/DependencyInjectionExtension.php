<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.core.repository.content_object.learning_path';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\Repository\ContentObject\LearningPath' => ['repository.xml', 'services.xml']];
    }
}