<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;

/**
 * Better storage for home elements using content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublication extends Publication
{
    public const PROPERTY_ELEMENT_ID = 'element_id';

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_ELEMENT_ID));
    }

    /**
     *
     * @return int
     */
    public function get_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'home_content_object_publication';
    }

    /**
     *
     * @param int $element_id
     */
    public function set_element_id($element_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $element_id);
    }
}