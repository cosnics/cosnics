<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableDataProvider
{
    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}