<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableColumnModel
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableColumnModel
{
    /**
     * @return string
     */
    function getEntryClassName()
    {
        return $this->getTable()->getLearningPathAssignmentService()->getEntryClassName();
    }

    /**
     * @return string
     */
    function getScoreClassName()
    {
        return $this->getTable()->getLearningPathAssignmentService()->getScoreClassName();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry\EntryTable|\Chamilo\Libraries\Format\Table\Table
     */
    public function getTable()
    {
        return $this->get_table();
    }
}