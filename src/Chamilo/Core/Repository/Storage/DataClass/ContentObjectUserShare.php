<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package repository.lib
 */
/**
 *
 * @author Sven Vanpoucke
 */
class ContentObjectUserShare extends ContentObjectShare
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_USER_ID = 'user_id';
    const TYPE_USER_SHARE = 'user';

    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    public function get_user()
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieve(User :: class_name(), (int) $this->get_user_id());
    }

    /**
     * Get the default properties of all groups.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID));
    }
}
