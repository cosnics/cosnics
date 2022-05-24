<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * * *************************************************************************** Data provider for an unsubscribed
 * course user browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedUserTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_users_not_subscribed_to_course(
            $this->get_component()->get_course_id(), $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieve_users_not_subscribed_to_course(
            $this->get_component()->get_course_id(), $condition, $offset, $count, $orderBy
        );
    }
}
