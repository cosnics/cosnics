<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass;

/**
 * Generic portfolio notification object
 * 
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Notification extends \Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification
{
    // Properties
    const PROPERTY_COMPLEX_CONTENT_OBJECT_ID = 'complex_content_object_id';

    /**
     * Get the default properties of all feedback
     * 
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_COMPLEX_CONTENT_OBJECT_ID));
    }

    /**
     *
     * @return int
     */
    public function get_complex_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COMPLEX_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param int $complex_content_object_id
     */
    public function set_complex_content_object_id($complex_content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COMPLEX_CONTENT_OBJECT_ID, $complex_content_object_id);
    }
}