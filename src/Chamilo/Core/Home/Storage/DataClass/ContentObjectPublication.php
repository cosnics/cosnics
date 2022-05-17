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
    const PROPERTY_ELEMENT_ID = 'element_id';

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_ELEMENT_ID));
    }

    /**
     *
     * @return integer
     */
    public function get_element_id()
    {
        return $this->get_default_property(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'home_content_object_publication';
    }

    /**
     *
     * @param integer $element_id
     */
    public function set_element_id($element_id)
    {
        $this->set_default_property(self::PROPERTY_ELEMENT_ID, $element_id);
    }
}