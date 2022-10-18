<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */

/**
 * Data provider for a repository browser table.
 * This class implements some functions to allow repository browser tables
 * to retrieve information about the learning objects to display.
 */
class AdminRequestTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(CourseRequest::class, new DataClassCountParameters($condition));
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieves(
            CourseRequest::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
