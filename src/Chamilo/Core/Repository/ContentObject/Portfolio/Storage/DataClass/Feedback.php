<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass;

/**
 * Generic portfolio feedback object
 * 
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Feedback extends \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
{
    // Properties
    const PROPERTY_COMPLEX_CONTENT_OBJECT_ID = 'complex_content_object_id';

    /**
     * Get the default properties of all feedback
     * 
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_COMPLEX_CONTENT_OBJECT_ID));
    }

    /**
     *
     * @return int
     */
    public function get_complex_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_COMPLEX_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param int $complex_content_object_id
     */
    public function set_complex_content_object_id($complex_content_object_id)
    {
        $this->set_default_property(self::PROPERTY_COMPLEX_CONTENT_OBJECT_ID, $complex_content_object_id);
    }
}