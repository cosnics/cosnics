<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroupTableRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package application.lib.weblcms.tool.course_group.component
 */
class BrowserComponent extends TabComponent
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    public function getCourseGroupCondition(): ?AndCondition
    {
        $conditions = [];

        $properties = [];
        $properties[] = new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME);
        $properties[] = new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_DESCRIPTION);
        $query_condition = $this->getButtonToolbarRenderer()->getConditions($properties);

        $root_course_group = $this->rootCourseGroup;

        $course_group_id = $this->get_group_id();

        if (!$course_group_id || ($root_course_group->get_id() == $course_group_id))
        {
            $root_course_group_id = $root_course_group->get_id();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable($root_course_group_id)
            );
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable($course_group_id)
            );
        }

        if ($query_condition)
        {
            $conditions[] = $query_condition;
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }

        return null;
    }

    public function getCourseGroupTableRenderer(): CourseGroupTableRenderer
    {
        return $this->getService(CourseGroupTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \TableException
     */
    protected function renderTabContent(): string
    {
        $totalNumberOfItems =
            DataManager::count(CourseGroup::class, new DataClassCountParameters($this->getCourseGroupCondition()));
        $courseGroupTableRenderer = $this->getCourseGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $courseGroupTableRenderer->getParameterNames(), $courseGroupTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $courseGroups = DataManager::retrieves(
            CourseGroup::class, new DataClassRetrievesParameters(
                $this->getCourseGroupCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $courseGroupTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $courseGroupTableRenderer->legacyRender($this, $tableParameterValues, $courseGroups);
    }
}
