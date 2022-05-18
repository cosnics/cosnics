<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider;

use Chamilo\Core\Metadata\Provider\PropertyProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * This class provides and renders the properties to link to a metadata element
 * 
 * @package repository\integration\repository\content_object_metadata_element_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ContentObjectPropertyProvider implements PropertyProviderInterface
{
    use ClassContext;
    
    // Properties
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
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
            self::PROPERTY_TITLE, 
            self::PROPERTY_DESCRIPTION, 
            self::PROPERTY_OWNER_FULLNAME, 
            self::PROPERTY_CREATION_DATE, 
            self::PROPERTY_MODIFICATION_DATE, 
            self::PROPERTY_TYPE, 
            self::PROPERTY_IDENTIFIER);
    }

    /**
     * Renders a property for a given content object
     * 
     * @param string $property
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return string
     */
    public function renderProperty($property, DataClass $contentObject)
    {
        switch ($property)
        {
            case self::PROPERTY_OWNER_FULLNAME :
                $author = DataManager::retrieve_by_id(
                    User::class,
                    $contentObject->get_owner_id());
                if ($author)
                {
                    return $author->get_fullname();
                }
                
                return Translation::get('UserUnknown', null, Manager::context());
            case self::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::format_locale_date(null, $contentObject->get_creation_date());
            case self::PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities::format_locale_date(null, $contentObject->get_modification_date());
            case self::PROPERTY_IDENTIFIER :
                return '\Chamilo\Core\Repository\ContentObject:' . $contentObject->get_id();
        }
        
        if ($contentObject->isDefaultPropertyName($property))
        {
            return $contentObject->getDefaultProperty($property);
        }
        else
        {
            return $contentObject->getAdditionalProperty($property);
        }
    }
}