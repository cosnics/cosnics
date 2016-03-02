<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Storage;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Rights\RightsLocation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
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
    const PREFIX = 'weblcms_';

    /**
     * Copies the publications and categories to other courses.
     *
     * @param int[] $courses_ids
     * @param int[] $publications_ids
     * @param int[] $categories_ids
     *
     * @return bool
     */
    public static function copy_publications_and_categories($courses_ids, $publications_ids, $categories_ids)
    {
        $category_parent_ids_mapping = self::copy_categories($courses_ids, $categories_ids);
        if (!$category_parent_ids_mapping)
        {
            return false;
        }

        $success = self::copy_publications($courses_ids, $publications_ids, $category_parent_ids_mapping);

        return $success;
    }

    /**
     * Copies the publications to other courses in the root of their tools
     *
     * @param int[] $courses_ids
     * @param int[] $publications_ids
     *
     * @return bool
     */
    public static function copy_publications_to_root($courses_ids, $publications_ids)
    {
        return self::copy_publications($courses_ids, $publications_ids, array());
    }

    /**
     * Copies the publications to other courses.
     *
     * @param int[] $courses_ids
     * @param int[] $publications_ids
     * @param int[] $category_parent_ids_mapping
     *
     * @return bool
     */
    protected static function copy_publications($courses_ids, $publications_ids, $category_parent_ids_mapping = array())
    {
        $success = true;

        foreach ($publications_ids as $id)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication:: class_name(), $id
            );

            $possible_publication_class =
                'Chamilo\Application\Weblcms\Tool\\' . $publication->get_tool() . '\\Storage\\DataClass\\Publication';
            $publication_extension_exists = class_exists($possible_publication_class);

            if ($publication_extension_exists)
            {
                $datamanager_class =
                    'Chamilo\Application\Weblcms\Tool\\' . $publication->get_tool() . '\\Storage\\DataManager';

                $publication_extension = $datamanager_class:: retrieve(
                    $possible_publication_class,
                    new DataClassRetrieveParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                $possible_publication_class, $possible_publication_class::PROPERTY_PUBLICATION_ID
                            ),
                            new StaticConditionVariable($publication->get_id())
                        )
                    )
                );
            }

            $parent = $publication->get_category_id();

            foreach ($courses_ids as $course_code)
            {
                $publication->set_id(null);
                $publication->set_course_id($course_code);

                if ($parent != 0)
                {
                    $publication->set_category_id($category_parent_ids_mapping[$course_code][$parent]);
                }

                $result = $publication->create();
                if (!($result instanceof RightsLocation))
                {
                    $success = false;
                }
                else
                {
                    if ($publication_extension_exists)
                    {
                        if ($publication_extension instanceof DataClass)
                        {
                            $publication_extension->set_publication_id($publication->get_id());
                            $success = ($success && $publication_extension->create());
                        }
                        else
                        {
                            $success = false;
                        }
                    }
                }
            }
        }

        return $success;
    }

    /**
     * Copies the categories to other courses.
     *
     * @param int[] $courses_ids
     * @param int[] $categories_ids
     *
     * @return array|bool
     */
    protected static function copy_categories($courses_ids, $categories_ids)
    {
        $category_parent_ids_mapping = array();
        $success = true;

        $condition = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(), ContentObjectPublicationCategory::PROPERTY_ID
            ), $categories_ids
        );
        $order_by = array();

        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(), ContentObjectPublicationCategory::PROPERTY_PARENT
            )
        );

        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(), ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER
            )
        );

        $categories = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            ContentObjectPublicationCategory:: class_name(),
            new DataClassRetrievesParameters($condition, null, null, $order_by)
        );

        while ($category = $categories->next_result())
        {
            $parent_id = $category->get_parent();
//            if (!$category->get_allow_change())
//            {
//                continue; // Because the course groups are not copied this won't copy their corresponding folders
//            }

            $old_id = $category->get_id();

            foreach ($courses_ids as $course_code)
            {
                $category->set_id(null);
                $category->set_allow_change(true);
                $category->set_course($course_code);

                if ($parent_id != 0)
                {
                    $category->set_parent($category_parent_ids_mapping[$course_code][$parent_id]);
                }

                if (!$category->create())
                {
                    $success = false;
                }

                $category_parent_ids_mapping[$course_code][$old_id] = $category->get_id();
            }
        }

        if (!$success)
        {
            return false;
        }
        else
        {
            return $category_parent_ids_mapping;
        }
    }
}
