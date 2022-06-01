<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\Instance\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PersonalInstance extends Instance
{
    const PROPERTY_USER_ID = 'user_id';

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_USER_ID);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_instance_personal_instance';
    }

    public function get_user_id()
    {
        return $this->getAdditionalProperty(self::PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        if (isset($user_id) && strlen($user_id) > 0)
        {
            $this->setAdditionalProperty(self::PROPERTY_USER_ID, $user_id);
        }
    }
}
