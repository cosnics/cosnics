<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $learningPathAssignmentService = $this->getTable()->getLearningPathAssignmentService();

        return $learningPathAssignmentService->countEntriesForTreeNodeDataEntityTypeAndId(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getTreeNodeData(),
            Entry::ENTITY_TYPE_USER, $this->getTable()->getEntityId(), $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $learningPathAssignmentService = $this->getTable()->getLearningPathAssignmentService();

        return $learningPathAssignmentService->findEntriesForTreeNodeDataEntityTypeAndId(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getTreeNodeData(),
            Entry::ENTITY_TYPE_USER, $this->getTable()->getEntityId(), $condition, $offset, $count, $orderBy
        );
    }
}