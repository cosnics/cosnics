<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Table\UnsubscribedCourseTableRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a browser for the courses where a user is not subscribed to
 *
 * @package \application\weblcms\course
 * @author  Yannick & Tristan
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseUnsubscribedCoursesComponent extends BrowseSubscriptionCoursesComponent
{

    public function getCourseCondition(): ?AndCondition
    {
        $conditions = [];

        $parent_condition = parent::getCourseCondition();
        if ($parent_condition)
        {
            $conditions[] = $parent_condition;
        }

        $user = $this->getUser();

        $userConditions = [];
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($user->getId())
        );
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
        );

        $conditions[] = new NotCondition(
            new SubselectCondition(
                new PropertyConditionVariable(Course::class, DataClass::PROPERTY_ID), new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
            ), new AndCondition($userConditions)
            )
        );

        $userGroupIdentifiers =
            $this->getGroupsTreeTraverser()->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        if ($userGroupIdentifiers)
        {
            $groupsConditions = [];
            $groupsConditions[] = new InCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                ), $userGroupIdentifiers
            );
            $groupsConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
            );

            $conditions[] = new NotCondition(
                new SubselectCondition(
                    new PropertyConditionVariable(Course::class, DataClass::PROPERTY_ID), new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new AndCondition($groupsConditions)
                )
            );
        }

        return new AndCondition($conditions);
    }

    protected function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    public function getUnsubscribedCourseTableRenderer(): UnsubscribedCourseTableRenderer
    {
        return $this->getService(UnsubscribedCourseTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count_courses_with_course_type($this->getCourseCondition());
        $unsubscribedCourseTableRenderer = $this->getUnsubscribedCourseTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $unsubscribedCourseTableRenderer->getParameterNames(),
            $unsubscribedCourseTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $courses = DataManager::retrieve_courses_with_course_type(
            $this->getCourseCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $unsubscribedCourseTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $unsubscribedCourseTableRenderer->render($tableParameterValues, $courses);
    }
}
