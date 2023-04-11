<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;

/**
 * Better storage for home elements using content objects
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPublication extends Publication
{
    public const PROPERTY_ELEMENT_ID = 'element_id';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_ELEMENT_ID]);
    }

    public static function getStorageUnitName(): string
    {
        return 'home_content_object_publication';
    }

    public function get_element_id(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    public function set_element_id(int $element_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $element_id);
    }
}