<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableColumnModel
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableColumnModel
{
    /**
     * @return string
     */
    public function getEntryClassName()
    {
        return $this->getTable()->getLearningPathAssignmentService()->getEntryClassName();
    }

    /**
     * @return string
     */
    public function getScoreClassName()
    {
        return $this->getTable()->getLearningPathAssignmentService()->getScoreClassName();
    }
}
