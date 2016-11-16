<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Description of external_repository_user_quotumclass
 * 
 * @author jevdheyd
 */
class UserQuotum extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_QUOTUM = 'quotum';

    /**
     * Get the default properties of all server_objects.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_EXTERNAL_REPOSITORY_ID, self::PROPERTY_USER_ID, self::PROPERTY_QUOTUM));
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_external_repository_id($external_repository_id)
    {
        $this->set_default_property(self::PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    public function get_external_repository_id()
    {
        return $this->get_default_property(self::PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    public function set_quotum($quotum)
    {
        $this->set_default_property(self::PROPERTY_QUOTUM, $quotum);
    }

    public function get_quotum()
    {
        return $this->get_default_property(self::PROPERTY_QUOTUM);
    }
}
