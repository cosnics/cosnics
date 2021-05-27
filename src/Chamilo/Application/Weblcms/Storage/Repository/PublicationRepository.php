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

/**
 * The repository for the publication
 *
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository implements PublicationRepositoryInterface
{

    /**
     * Finds one publication by a given id
     *
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function findPublicationById($publicationId)
    {
        return DataManager::retrieve_by_id(ContentObjectPublication::class, $publicationId);
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
        return DataManager::retrieves(ContentObjectPublicationCategory::class, $condition);
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
        $conditions = [];

        $conditions[] = $this->getPublicationCategoryConditionForCourseAndTool($course, $tool);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), ComparisonCondition::EQUAL, new StaticConditionVariable($categoryId)
        );

        return $this->findPublicationCategoriesByCondition(new AndCondition($conditions));
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
        return DataManager::retrieve_by_id(ContentObjectPublicationCategory::class, $categoryId);
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
        $conditions = [];

        $conditions[] = $this->getPublicationConditionForCourseAndTool($course, $tool);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
            ), ComparisonCondition::EQUAL, new StaticConditionVariable($categoryId)
        );

        return $this->findPublicationsByCondition(new AndCondition($conditions));
    }

    /**
     * Finds publications by a given condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return mixed[] | ContentObjectPublication[]
     */
    protected function findPublicationsByCondition(Condition $condition)
    {
        return DataManager::retrieve_content_object_publications($condition);
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
     * @param array $publicationIds
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication[]
     */
    public function findPublicationsByIds(array $publicationIds = [])
    {
        return $this->findPublicationsByCondition(
            new InCondition(
                new PropertyConditionVariable(ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID),
                $publicationIds
            )
        );
    }

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
     * Returns the course groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return CourseGroup[]
     */
    public function findTargetCourseGroupsForPublication(ContentObjectPublication $publication)
    {
        return DataManager::retrieve_publication_target_course_groups(
            $publication->get_id(), $publication->get_course_id()
        );
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
            $publication->get_id(), $publication->get_course_id()
        );
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
        return DataManager::retrieve_publication_target_users($publication->get_id(), $publication->get_course_id());
    }

    /**
     * Finds the publications for which the properties are set to visible by a given set of publication ids
     *
     * @param int[] $publicationIds
     *
     * @return mixed
     */
    public function findVisiblePublicationsByIds(array $publicationIds = [])
    {
        $conditions = [];

        $from_date_variables = new PropertyConditionVariable(
            ContentObjectPublication::class, ContentObjectPublication::PROPERTY_FROM_DATE
        );

        $to_date_variable = new PropertyConditionVariable(
            ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TO_DATE
        );

        $time_conditions = [];

        $time_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_HIDDEN
            ), ComparisonCondition::EQUAL, new StaticConditionVariable(0)
        );

        $forever_conditions = [];

        $forever_conditions[] = new ComparisonCondition(
            $from_date_variables, ComparisonCondition::EQUAL, new StaticConditionVariable(0)
        );

        $forever_conditions[] = new ComparisonCondition(
            $to_date_variable, ComparisonCondition::EQUAL, new StaticConditionVariable(0)
        );

        $forever_condition = new AndCondition($forever_conditions);

        $between_conditions = [];

        $between_conditions[] = new ComparisonCondition(
            $from_date_variables, ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(time())
        );

        $between_conditions[] = new ComparisonCondition(
            $to_date_variable, ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(time())
        );

        $between_condition = new AndCondition($between_conditions);

        $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));

        $conditions[] = new AndCondition($time_conditions);

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID
            ), $publicationIds
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieve_content_object_publications($condition);
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
        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), ComparisonCondition::EQUAL, new StaticConditionVariable($course->get_id())
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), ComparisonCondition::EQUAL, new StaticConditionVariable($tool)
        );

        return new AndCondition($conditions);
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
        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), ComparisonCondition::EQUAL, new StaticConditionVariable($course->get_id())
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
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), ComparisonCondition::EQUAL, new StaticConditionVariable($tool)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
     *
     * @return int[]
     */
    public function getTargetUserIdsForPublication(ContentObjectPublication $publication)
    {
        return DataManager::getPublicationTargetUserIds($publication->getId(), $publication->get_course_id());
    }
}