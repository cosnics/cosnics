<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Storage;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the data manager for this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package application.weblcms.tool.assignment
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    public const PREFIX = 'weblcms_';

    /**
     * checks whether a category can be deleted
     *
     * @param int $category_id
     *
     * @return bool
     */
    private static function allowed_to_delete_category($category_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
            ), new StaticConditionVariable($category_id)
        );

        $condition = new AndCondition($conditions);

        $count = \Chamilo\Application\Weblcms\Storage\Datamanager::count_content_object_publications($condition);

        if ($count > 0)
        {
            return false;
        }

        return !self::have_subcategories_publications($category_id);
    }

    /**
     * Counts the number of course sections that are of the type "custom" of a certain course.
     *
     * @param int $course_id
     *
     * @return int
     */
    public static function count_custom_course_sections_from_course($course_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_TYPE),
            new StaticConditionVariable(CourseSection::TYPE_CUSTOM)
        );

        $condition = new AndCondition($conditions);

        return self::count(CourseSection::class, new DataClassParameters(condition: $condition));
    }

    /**
     *
     * @param int[] $course_categories_ids
     *
     * @return bool
     */
    protected static function delete_categories($categories_ids)
    {
        $success = true;

        foreach ($categories_ids as $id)
        {
            $category = self::retrieve_content_object_publication_category($id);
            if (!$category)
            {
                continue;
            }

            $success = self::delete_categories_recursive($id);
            if (!$success)
            {
                break;
            }
        }

        return $success;
    }

    /**
     * Will check if the category and its subcategories has publications.
     * If not, it will delete the subcategories and the category.
     *
     * @param $category_id
     *
     * @return bool
     */
    protected static function delete_categories_recursive($category_id)
    {
        $continue = true;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), new StaticConditionVariable($category_id)
        );

        $children = self::retrieves(ContentObjectPublicationCategory::class, $condition);
        foreach ($children as $child)
        {
            $continue = self::delete_categories_recursive($child);
        }

        if ($continue)
        {
            if (self::allowed_to_delete_category($category_id))
            {
                $category = self::retrieve_content_object_publication_category($category_id);

                return $category->delete();
            }
        }

        return false;
    }

    /**
     * Deletes course sections
     *
     * @param int[] $course_sections_ids
     *
     * @return bool
     */
    public static function delete_course_sections($course_sections_ids)
    {
        $success = true;

        $condition = new InCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_ID), $course_sections_ids
        );

        $course_sections = \Chamilo\Application\Weblcms\Storage\Datamanager::retrieves(
            CourseSection::class, new RetrievesParameters(condition: $condition)
        );

        foreach ($course_sections as $course_section)
        {
            if (!$course_section->delete())
            {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Deletes publications.
     *
     * @param int[] $publications_ids
     *
     * @return bool
     */
    public static function delete_publications($publications_ids)
    {
        $success = true;

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID),
            $publications_ids
        );

        $publications = \Chamilo\Application\Weblcms\Storage\Datamanager::retrieves(
            ContentObjectPublication::class, $condition
        );

        foreach ($publications as $publication)
        {
            if (!$publication->delete())
            {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Deletes publications and categories, starting with publications.
     * Will not continue if a delete fails.
     *
     * @param int[] $publications_ids
     * @param int[] $categories_ids
     *
     * @return bool
     */
    public static function delete_publications_and_categories($publications_ids, $categories_ids)
    {
        $success = true;
        $success == $success && self::delete_publications($publications_ids);
        if (!$success)
        {
            return false;
        }

        $success == $success && self::delete_categories($categories_ids);

        return $success;
    }

    /**
     * Checks if the subcategories of a category has publications.
     *
     * @param int $category_id
     *
     * @return bool
     */
    protected static function have_subcategories_publications($category_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), new StaticConditionVariable($category_id)
        );

        $subcategories = \Chamilo\Application\Weblcms\Storage\Datamanager::retrieves(
            ContentObjectPublicationCategory::class, $condition
        );

        foreach ($subcategories as $cat)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
                ), new StaticConditionVariable($cat->get_id())
            );

            $count = \Chamilo\Application\Weblcms\Storage\Datamanager::count_content_object_publications($condition);

            if ($count > 0 || self::have_subcategories_publications($cat->get_id()))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve a certain ContentObjectPublicationCategory.
     *
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public static function retrieve_content_object_publication_category($id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_ID
            ), new StaticConditionVariable($id)
        );

        return self::retrieve(
            ContentObjectPublicationCategory::class, new DataClassParameters(condition: $condition)
        );
    }

    /**
     * Retrieves the course sections that are of the type "custom" of a certain course.
     *
     * @param int $course_id
     *
     * @return array
     */
    public static function retrieve_custom_course_sections_as_array($course_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_TYPE),
            new StaticConditionVariable(CourseSection::TYPE_CUSTOM)
        );

        $condition = new AndCondition($conditions);

        $course_sections_set = self::retrieves(CourseSection::class, $condition);

        $course_sections = [];
        foreach ($course_sections_set as $course_section)
        {
            $course_sections[] = $course_section->getDefaultProperties();
        }

        return $course_sections;
    }
}
