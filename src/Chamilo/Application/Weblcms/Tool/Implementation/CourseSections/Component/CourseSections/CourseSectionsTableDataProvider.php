<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component\CourseSections;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */

/**
 * Data provider for a repository browser table.
 * This class implements some functions to allow repository browser tables
 * to retrieve information about the learning objects to display.
 */
class CourseSectionsTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(
            CourseSection::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $orderBy = new OrderBy(array(
            new OrderProperty(
                new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_DISPLAY_ORDER)
            )
        ));

        return DataManager::retrieves(
            CourseSection::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
