<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * Base class to generate actions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class NodeActionGenerator extends ActionGenerator
{
    /**
     * Generates the acions for a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param bool $canEditLearningPathTreeNode
     *
     * @return array|Action[]
     */
    abstract public function generateNodeActions(
        LearningPathTreeNode $learningPathTreeNode, $canEditLearningPathTreeNode = false
    ): array;
}