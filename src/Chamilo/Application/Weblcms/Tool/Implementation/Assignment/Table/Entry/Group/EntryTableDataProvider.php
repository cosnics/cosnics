<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntryTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableDataProvider
{
    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTable | \Chamilo\Libraries\Format\Table\Table
     */
    public function getTable()
    {
        return $this->get_table();
    }
}