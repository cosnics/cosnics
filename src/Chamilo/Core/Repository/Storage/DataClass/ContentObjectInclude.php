<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ContentObjectInclude extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_INCLUDE_ID = 'include_id';

    private $include_object;

    /**
     * Get the default properties of all content object attachments.
     *
     * @param array $extended_property_names
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_INCLUDE_ID));
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_include_id()
    {
        return $this->get_default_property(self :: PROPERTY_INCLUDE_ID);
    }

    public function set_include_id($include_id)
    {
        $this->set_default_property(self :: PROPERTY_INCLUDE_ID, $include_id);
    }

    public function get_include_object()
    {
        if (! isset($this->include_object))
        {
            $this->include_object = DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $this->get_include_id());
        }
        return $this->include_object;
    }
}
