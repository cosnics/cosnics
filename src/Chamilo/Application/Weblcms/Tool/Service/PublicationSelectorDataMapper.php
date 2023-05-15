<?php
namespace Chamilo\Application\Weblcms\Tool\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * DataMapper service that changes publications and publication categories for the publication selector
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationSelectorDataMapper
{
    const PROPERTY_CONTENT_OBJECT_CLASS = 'content_object_class';

    const PROPERTY_TOOL_CLASS = 'tool_class';

    /**
     * Retrieves content object publication categories and transforms them as an array for the publication selector
     *
     * @param int $course_id
     * @param array $tools
     *
     * @return array
     */
    public static function getContentObjectPublicationCategoriesForPublicationSelector($course_id, $tools = [])
    {
        $order_by = OrderBy::generate(
            ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER
        );

        $publication_categories_set = DataManager::retrieve_content_object_publication_categories_from_course(
            $course_id, $tools, $order_by
        );

        $categories = [];

        foreach ($publication_categories_set as $category)
        {
            $properties = $category->getDefaultProperties();
            $glyphNamespace = 'Chamilo\Application\Weblcms\Tool\Implementation\\' .
                $properties[ContentObjectPublicationCategory::PROPERTY_TOOL];

            $glyph = new NamespaceIdentGlyph(
                $glyphNamespace, true, false, false, IdentGlyph::SIZE_MINI, array('fa-fw')
            );

            $properties[self::PROPERTY_TOOL_CLASS] = $glyph->getClassNamesString();
            $categories[] = $properties;
        }

        return $categories;
    }

    /**
     * Retrieves content object publications and transforms them as an array for the publication selector
     *
     * @param int $course_id
     *
     * @return array
     */
    public function getContentObjectPublicationsForPublicationSelector($course_id)
    {
        $propertyNames = [];
        $propertyNames[] = ContentObjectPublication::PROPERTY_ID;
        $propertyNames[] = ContentObjectPublication::PROPERTY_CATEGORY_ID;
        $propertyNames[] = ContentObject::PROPERTY_TITLE;
        $propertyNames[] = ContentObjectPublication::PROPERTY_TOOL;

        $order_by =
            OrderBy::generate(ContentObjectPublication::class, ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX);

        $publications_set = DataManager::retrieve_content_object_publications_from_course(
            $course_id, $order_by
        );

        $publications = [];

        if (count($propertyNames) == 0)
        {
            return $publications_set;
        }

        foreach ($publications_set as $publication)
        {
            $publicationProperties = [];

            foreach ($propertyNames as $property)
            {
                $publicationProperties[$property] = $publication[$property];
            }

            $toolGlyphNamespace = 'Chamilo\Application\Weblcms\Tool\Implementation\\' .
                $publication[ContentObjectPublicationCategory::PROPERTY_TOOL];

            $toolGlyph = new NamespaceIdentGlyph(
                $publication[ContentObject::PROPERTY_TYPE]::CONTEXT, true, false, false, IdentGlyph::SIZE_MINI,
                array('fa-fw')
            );

            $contentObjectGlyph = new NamespaceIdentGlyph(
                $toolGlyphNamespace, true, false, false, IdentGlyph::SIZE_MINI, array('fa-fw')
            );

            $publicationProperties[self::PROPERTY_TOOL_CLASS] = $toolGlyph->getClassNamesString();
            $publicationProperties[self::PROPERTY_CONTENT_OBJECT_CLASS] = $contentObjectGlyph->getClassNamesString();

            $publications[] = $publicationProperties;
        }

        return $publications;
    }
}