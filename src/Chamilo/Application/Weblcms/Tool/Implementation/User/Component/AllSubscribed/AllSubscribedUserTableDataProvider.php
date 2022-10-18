<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribed;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Data provider for an all subscribed course user browser table, including users subscribed through (sub-)groups.
 *
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class AllSubscribedUserTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_all_course_users(
            $this->get_component()->get_course_id(), $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_all_course_users(
            $this->get_component()->get_course_id(), $condition, $offset, $count, $orderBy
        );
    }
}
