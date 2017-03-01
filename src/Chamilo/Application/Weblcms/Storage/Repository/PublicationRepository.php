<?php
namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\PublicationRepositoryInterface;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\DataClassRecordResultSet;

/**
 * The repository for the publication
 *
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository implements PublicationRepositoryInterface
{

    /**
     * Finds publications for a given tool
     *
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationsByTool($tool)
    {
        return $this->findPublicationsByCondition($this->getPublicationConditionForTool($tool));
    }

    /**
     * Finds publications for a given course and tool
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationsByCourseAndTool(Course $course, $tool)
    {
        return $this->findPublicationsByCondition($this->getPublicationConditionForCourseAndTool($course, $tool));
    }

    /**
     * Finds publications for a given course, tool and category
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     * @param int $categoryId
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationsByCategoryId(Course $course, $tool, $categoryId)
    {
        $conditions = array();

        $conditions[] = $this->getPublicationConditionForCourseAndTool($course, $tool);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_CATEGORY_ID
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($categoryId)
        );

        return $this->findPublicationsByCondition(new AndCondition($conditions));
    }

    /**
     * Returns a condition to retrieve ContentObjectPublication objects by a given course and tool
     *
     * @param Course $course
     * @param string $tool
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getPublicationConditionForCourseAndTool(Course $course, $tool)
    {
        $conditions = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_COURSE_ID
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($course->get_id())
        );

        $conditions[] = $this->getPublicationConditionForTool($tool);
        
        return new AndCondition($conditions);
    }

    /**
     * Returns a condition to retrieve ContentObjectPublication objects by a given tool
     *
     * @param string $tool
     *
     * @return ComparisonCondition
     */
    protected function getPublicationConditionForTool($tool)
    {
        return new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_TOOL
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($tool)
        );
    }

    /**
     * Finds publications by a given condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return ContentObjectPublication[]
     */
    protected function findPublicationsByCondition(Condition $condition)
    {
        $result = new DataClassRecordResultSet(
            ContentObjectPublication::class_name(),
            DataManager::retrieve_content_object_publications($condition)
        );

        return $result->as_array();
    }

    /**
     * Finds the publications for which the properties are set to visible by a given set of publication ids
     *
     * @param int[] $publicationIds
     *
     * @return mixed
     */
    public function findVisiblePublicationsByIds(array $publicationIds = array())
    {
        $conditions = array();

        $from_date_variables = new PropertyConditionVariable(
            ContentObjectPublication::class_name(),
            ContentObjectPublication::PROPERTY_FROM_DATE
        );

        $to_date_variable = new PropertyConditionVariable(
            ContentObjectPublication::class_name(),
            ContentObjectPublication::PROPERTY_TO_DATE
        );

        $time_conditions = array();

        $time_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_HIDDEN
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable(0)
        );

        $forever_conditions = array();

        $forever_conditions[] = new ComparisonCondition(
            $from_date_variables,
            ComparisonCondition::EQUAL,
            new StaticConditionVariable(0)
        );

        $forever_conditions[] = new ComparisonCondition(
            $to_date_variable,
            ComparisonCondition::EQUAL,
            new StaticConditionVariable(0)
        );

        $forever_condition = new AndCondition($forever_conditions);

        $between_conditions = array();

        $between_conditions[] = new ComparisonCondition(
            $from_date_variables,
            ComparisonCondition::LESS_THAN_OR_EQUAL,
            new StaticConditionVariable(time())
        );

        $between_conditions[] = new ComparisonCondition(
            $to_date_variable,
            ComparisonCondition::GREATER_THAN_OR_EQUAL,
            new StaticConditionVariable(time())
        );

        $between_condition = new AndCondition($between_conditions);

        $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));

        $conditions[] = new AndCondition($time_conditions);

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), ContentObjectPublication::PROPERTY_ID
            ),
            $publicationIds
        );

        $condition = new AndCondition($conditions);

        $result = new DataClassRecordResultSet(
            ContentObjectPublication::class_name(),
            DataManager::retrieve_content_object_publications($condition)
        );

        return $result->as_array();
    }

    /**
     * Finds one publication by a given id
     *
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function findPublicationById($publicationId)
    {
        return DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $publicationId);
    }

    /**
     * Returns the users for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return User[]
     */
    public function findTargetUsersForPublication(ContentObjectPublication $publication)
    {
        return DataManager::retrieve_publication_target_users($publication->get_id(), $publication->get_course_id())
            ->as_array();
    }

    /**
     * Returns the course groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return CourseGroup[]
     */
    public function findTargetCourseGroupsForPublication(ContentObjectPublication $publication)
    {
        return DataManager::retrieve_publication_target_course_groups(
            $publication->get_id(),
            $publication->get_course_id()
        )->as_array();
    }

    /**
     * Returns the platform groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return Group[]
     */
    public function findTargetPlatformGroupsForPublication(ContentObjectPublication $publication)
    {
        return DataManager::retrieve_publication_target_platform_groups(
            $publication->get_id(),
            $publication->get_course_id()
        )->as_array();
    }

    /**
     * Finds publication categories for a given course and tool
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     *
     * @return ContentObjectPublicationCategory[]
     */
    public function findPublicationCategoriesByCourseAndTool(Course $course, $tool)
    {
        return $this->findPublicationCategoriesByCondition(
            $this->getPublicationCategoryConditionForCourseAndTool($course, $tool)
        );
    }

    /**
     * Finds publications for a given course, tool and category
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     * @param int $categoryId
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationCategoriesByParentCategoryId(Course $course, $tool, $categoryId)
    {
        $conditions = array();

        $conditions[] = $this->getPublicationCategoryConditionForCourseAndTool($course, $tool);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_PARENT
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($categoryId)
        );

        return $this->findPublicationCategoriesByCondition(new AndCondition($conditions));
    }

    /**
     * Returns a condition to retrieve ContentObjectPublication objects by a given course and tool
     *
     * @param Course $course
     * @param int $tool
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getPublicationCategoryConditionForCourseAndTool(Course $course, $tool)
    {
        $conditions = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_COURSE
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($course->get_id())
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_TOOL
            ),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($tool)
        );

        return new AndCondition($conditions);
    }

    /**
     * Finds publications by a given condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return ContentObjectPublication[]
     */
    protected function findPublicationCategoriesByCondition(Condition $condition)
    {
        return DataManager::retrieves(ContentObjectPublicationCategory::class_name(), $condition)->as_array();
    }

    /**
     * Finds a publication category by a given id
     *
     * @param int $categoryId
     *
     * @return ContentObjectPublicationCategory
     */
    public function findPublicationCategoryById($categoryId)
    {
        return DataManager::retrieve_by_id(ContentObjectPublicationCategory::class_name(), $categoryId);
    }

    /**
     * Returns the target users of a content object publication
     *
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return array
     */
    public function findPublicationTargetUsers(ContentObjectPublication $contentObjectPublication)
    {
        return DataManager::get_publication_target_users($contentObjectPublication);
    }
}