<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableDataProvider extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableDataProvider
{
    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        return [];
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return 0;
    }
}