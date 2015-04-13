<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Ehb\Core\Metadata\Provider\PropertyProviderInterface;

/**
 * This class provides and renders the properties to link to a metadata element
 *
 * @package repository\integration\repository\content_object_metadata_element_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ContentObjectPropertyProvider implements PropertyProviderInterface
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    // Properties
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_TAGS = 'tags';
    const PROPERTY_OWNER_FULLNAME = 'owner_fullname';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_MODIFICATION_DATE = 'modification_date';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_IDENTIFIER = 'identifier';

    /**
     * Returns the properties that can be linked to the metadata elements
     *
     * @return string[]
     */
    public function getAvailableProperties()
    {
        return array(
            self :: PROPERTY_TITLE,
            self :: PROPERTY_DESCRIPTION,
            self :: PROPERTY_TAGS,
            self :: PROPERTY_OWNER_FULLNAME,
            self :: PROPERTY_CREATION_DATE,
            self :: PROPERTY_MODIFICATION_DATE,
            self :: PROPERTY_TYPE,
            self :: PROPERTY_IDENTIFIER);
    }

    /**
     * Renders a property for a given content object
     *
     * @param string $property
     * @param \core\repository\ContentObject $content_object
     *
     * @return string
     */
    public function renderProperty($property, ContentObject $content_object)
    {
        switch ($property)
        {
            case self :: PROPERTY_TAGS :
                $tags = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_tags_for_content_object(
                    $content_object->get_id());

                return implode(', ', $tags);
            case self :: PROPERTY_OWNER_FULLNAME :
                $author = \Chamilo\Core\User\Storage\DataManager :: retrieve(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    $content_object->get_owner_id());
                if ($author)
                {
                    return $author->get_fullname();
                }

                return Translation :: get('UserUnknown', null, \Chamilo\Core\User\Manager :: context());
            case self :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $content_object->get_creation_date());
            case self :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $content_object->get_modification_date());
            case self :: PROPERTY_IDENTIFIER :
                return '\core\repository\content_object:' . $content_object->get_id();
        }

        return $content_object->get_default_property($property);
    }
}