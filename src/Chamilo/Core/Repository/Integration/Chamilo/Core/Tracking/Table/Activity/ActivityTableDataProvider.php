<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Table data provider for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_activities($this->get_component()->get_current_content_object(), $condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieve_activities(
            $this->get_component()->get_current_content_object(), $condition, $offset, $count, $orderBy
        );
    }
}