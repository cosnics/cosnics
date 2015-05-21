<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

/**
 * $Id: content_object_pub_feedback.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib
 */
class ContentObjectPubFeedback extends ContentObject
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_ID = 'id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_CLOI_ID = 'complex_content_object_id';
    const PROPERTY_FEEDBACK_ID = 'feedback_id';

    /**
     * Default properties of the content_object_feedback object, stored in an associative array.
     */
    private $defaultProperties;

    public function __construct($publication_id = 0, $cloi_id = 0, $feedback_id = 0, $defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    public function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    public function get_default_properties()
    {
        return $this->defaultProperties;
    }

    public function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return array(
            self :: PROPERTY_ID, 
            self :: PROPERTY_PUBLICATION_ID, 
            self :: PROPERTY_CLOI_ID, 
            self :: PROPERTY_FEEDBACK_ID);
    }

    public function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    public static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    public function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    public function get_cloi_id()
    {
        return $this->get_default_property(self :: PROPERTY_CLOI_ID);
    }

    public function get_feedback_id()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
    }

    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    public function set_cloi_id($cloi_id)
    {
        return $this->set_default_property(self :: PROPERTY_CLOI_ID, $cloi_id);
    }

    public function set_feedback_id($feedback_id)
    {
        return $this->set_default_property(self :: PROPERTY_FEEDBACK_ID, $feedback_id);
    }

    public static function get_type_name()
    {
        return self :: get_table_name();
    }
}
