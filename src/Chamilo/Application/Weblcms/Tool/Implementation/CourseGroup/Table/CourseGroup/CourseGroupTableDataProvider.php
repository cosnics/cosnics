<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(CourseGroup::class, new DataClassCountParameters($condition));
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieves(
            CourseGroup::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
