<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTableDataProvider extends RecordTableDataProvider
{
    
    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_course_group_users(
            $this->get_component()->getCurrentCourseGroup()->get_id(), $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_course_group_users_with_subscription_time(
            $this->get_component()->getCurrentCourseGroup()->get_id(), $condition, $offset, $count, $orderBy
        );
    }
}
