<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\Group;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableColumnModel
    extends \Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\EntityTableColumnModel
{
    const PROPERTY_GROUP_MEMBERS = 'group_members';

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        parent::initializeColumns();
        $this->addColumn(new StaticTableColumn(self::PROPERTY_GROUP_MEMBERS), 1);
    }
}