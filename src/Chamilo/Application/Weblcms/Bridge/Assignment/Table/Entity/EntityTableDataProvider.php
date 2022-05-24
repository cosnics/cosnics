<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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
        return $this->getTable()->getEntityService()->countEntities(
            $this->getTable()->getContentObjectPublication(), $condition
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table |
     *     \Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\User\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getTable()->getEntityService()->retrieveEntities(
            $this->getTable()->getContentObjectPublication(), $condition, $offset, $count, $orderBy
        );
    }
}