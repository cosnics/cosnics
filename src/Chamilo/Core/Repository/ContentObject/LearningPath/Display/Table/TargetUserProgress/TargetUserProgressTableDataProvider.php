<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TargetUserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress\UserProgressTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TargetUserProgressTableDataProvider extends UserProgressTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->getTrackingService()->countTargetUsersWithLearningPathAttempts(
            $this->getLearningPath(), $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $this->cleanupOrderBy($orderBy);

        return $this->getTrackingService()->getTargetUsersWithLearningPathAttempts(
            $this->getLearningPath(), $this->getCurrentTreeNode(), $condition, $offset, $count, $orderBy
        );
    }
}