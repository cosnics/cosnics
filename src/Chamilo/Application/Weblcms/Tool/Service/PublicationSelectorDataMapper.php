<?php

namespace Chamilo\Application\Weblcms\Tool\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * DataMapper service that changes publications and publication categories for the publication selector
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationSelectorDataMapper
{
    /**
     * Retrieves content object publications and transforms them as an array for the publication selector
     *
     * @param int $course_id
     *
     * @return array
     */
    public function getContentObjectPublicationsForPublicationSelector(
        $course_id
    )
    {
        $properties = array();
        $properties[] = ContentObjectPublication::PROPERTY_ID;
        $properties[] = ContentObjectPublication::PROPERTY_CATEGORY_ID;
        $properties[] = ContentObject::PROPERTY_TITLE;
        $properties[] = ContentObjectPublication::PROPERTY_TOOL;

        $order_by = array();

        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
            )
        );

        $publications_set = \Chamilo\Application\Weblcms\Course\Storage\DataManager::
        retrieve_content_object_publications_from_course($course_id, $order_by);

        $publications = array();

        if (count($properties) == 0)
        {
            return $publications_set->as_array();
        }

        while ($publication = $publications_set->next_result())
        {
            $publication_array = array();
            foreach ($properties as $property)
            {
                $publication_array[$property] = $publication[$property];
            }
            $publications[] = $publication_array;
        }

        return $publications;
    }

    /**
     *  Retrieves content object publication categories and transforms them as an array for the publication selector
     *
     * @param int $course_id
     * @param array $tools
     *
     * @return array
     */
    public static function getContentObjectPublicationCategoriesForPublicationSelector(
        $course_id, $tools = array()
    )
    {
        $order_by = array();
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(), ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER
            )
        );

        $publication_categories_set = \Chamilo\Application\Weblcms\Course\Storage\DataManager::
        retrieve_content_object_publication_categories_from_course($course_id, $tools, $order_by);

        $categories = array();

        while ($category = $publication_categories_set->next_result())
        {
            $categories[] = $category->get_default_properties();
        }

        return $categories;
    }
}