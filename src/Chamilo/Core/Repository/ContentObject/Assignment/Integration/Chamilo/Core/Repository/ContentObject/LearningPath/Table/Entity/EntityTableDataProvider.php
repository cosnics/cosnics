<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->getTable()->getLearningPathAssignmentService()->countTargetUsersForTreeNodeData(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getTreeNodeData(),
            $this->getTable()->getUserIds(), $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getTable()->getLearningPathAssignmentService()->findTargetUsersForTreeNodeData(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getTreeNodeData(),
            $this->getTable()->getUserIds(), $condition, $offset, $count, $orderBy
        );
    }
}