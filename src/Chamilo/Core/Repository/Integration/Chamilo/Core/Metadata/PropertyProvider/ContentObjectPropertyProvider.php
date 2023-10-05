<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider;

use Chamilo\Core\Metadata\Provider\PropertyProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * This class provides and renders the properties to link to a metadata element
 *
 * @package repository\integration\repository\content_object_metadata_element_linker
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class ContentObjectPropertyProvider implements PropertyProviderInterface
{

    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_IDENTIFIER = 'identifier';
    public const PROPERTY_MODIFICATION_DATE = 'modification_date';
    public const PROPERTY_OWNER_FULLNAME = 'owner_fullname';
    public const PROPERTY_TITLE = 'title';
    public const PROPERTY_TYPE = 'type';

    /**
     * Returns the properties that can be linked to the metadata elements
     *
     * @return string[]
     */
    public function getAvailableProperties()
    {
        return [
            self::PROPERTY_TITLE,
            self::PROPERTY_DESCRIPTION,
            self::PROPERTY_OWNER_FULLNAME,
            self::PROPERTY_CREATION_DATE,
            self::PROPERTY_MODIFICATION_DATE,
            self::PROPERTY_TYPE,
            self::PROPERTY_IDENTIFIER
        ];
    }

    /**
     * Renders a property for a given content object
     *
     * @param string $property
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return string
     */
    public function renderProperty($property, DataClass $contentObject)
    {
        switch ($property)
        {
            case self::PROPERTY_OWNER_FULLNAME :
                $author = DataManager::retrieve_by_id(
                    User::class, $contentObject->get_owner_id()
                );
                if ($author)
                {
                    return $author->get_fullname();
                }

                return Translation::get('UserUnknown', null, Manager::CONTEXT);
            case self::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(null, $contentObject->get_creation_date());
            case self::PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(
                    null, $contentObject->get_modification_date()
                );
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