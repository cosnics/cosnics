<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry\User
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableDataProvider
{

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $learningPathAssignmentService = $this->getTable()->getAssignmentService();

        return $learningPathAssignmentService->findEntriesForContentObjectPublicationEntityTypeAndId(
            $this->getTable()->getContentObjectPublication(),
            $this->getTable()->getEntityType(),
            $this->getTable()->getEntityId(),
            $condition,
            $offset,
            $count,
            $orderBy
        );
    }

    public function countData(?Condition $condition = null): int
    {
        $learningPathAssignmentService = $this->getTable()->getAssignmentService();

        return $learningPathAssignmentService->countEntriesForContentObjectPublicationEntityTypeAndId(
            $this->getTable()->getContentObjectPublication(),
            $this->getTable()->getEntityType(),
            $this->getTable()->getEntityId(),
            $condition
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry\EntryTable | \Chamilo\Libraries\Format\Table\Table
     */
    public function getTable()
    {
        return $this->get_table();
    }
}