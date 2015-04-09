<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class ContentObjectTag extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TAG = 'tag';

    /**
     * Get the default properties of all content object attachments.
     * 
     * @param array $extended_property_names
     *
     * @return array
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        $extended_property_names[] = self :: PROPERTY_TAG;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @return string
     */
    public function get_tag()
    {
        return $this->get_default_property(self :: PROPERTY_TAG);
    }

    /**
     *
     * @param string $tag
     */
    public function set_tag($tag)
    {
        $this->set_default_property(self :: PROPERTY_TAG, $tag);
    }
}