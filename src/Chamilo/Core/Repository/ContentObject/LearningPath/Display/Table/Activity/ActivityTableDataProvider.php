<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Activity;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActivityService;
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Table data provider for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityTableDataProvider
    extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity\ActivityTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->getActivityService()->countActivitiesForTreeNode($this->getCurrentTreeNode());
    }

    /**
     * @return ActivityService
     */
    protected function getActivityService()
    {
        return $this->get_component()->getService(ActivityService::class);
    }

    /**
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return new DataClassCollection(
            $this->getActivityService()->retrieveActivitiesForTreeNode(
                $this->getCurrentTreeNode(), $offset, $count, $orderBy->getFirst()
            )
        );
    }
}