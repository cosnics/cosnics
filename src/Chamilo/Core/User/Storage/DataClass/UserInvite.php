<?php

namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserInvite extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_INVITED_BY_USER_ID = 'invited_by_user_id';
    const PROPERTY_SECURITY_KEY = 'secret_key';
    const PROPERTY_VALID_UNTIL = 'valid_until';

    /**
     * Get the default properties of all users.
     *
     * @param array $extended_property_names
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_USER_ID;
        $extended_property_names[] = self::PROPERTY_INVITED_BY_USER_ID;
        $extended_property_names[] = self::PROPERTY_SECURITY_KEY;
        $extended_property_names[] = self::PROPERTY_VALID_UNTIL;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * @return int
     */
    public function getInvitedByUserId()
    {
        return $this->get_default_property(self::PROPERTY_INVITED_BY_USER_ID);
    }

    /**
     * @return string
     */
    public function getSecurityKey()
    {
        return $this->get_default_property(self::PROPERTY_SECURITY_KEY);
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    public function getValidUntil()
    {
        $validUntilTimestamp =  $this->get_default_property(self::PROPERTY_VALID_UNTIL);

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($validUntilTimestamp);

        return $dateTime;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId(int $userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
        return $this;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setInvitedByUserId(int $userId)
    {
        $this->set_default_property(self::PROPERTY_INVITED_BY_USER_ID, $userId);
        return $this;
    }

    /**
     * @param string $securityKey
     *
     * @return $this
     */
    public function setSecurityKey(string $securityKey)
    {
        $this->set_default_property(self::PROPERTY_SECURITY_KEY, $securityKey);
        return $this;
    }

    /**
     * @param \DateTime $validUntil
     *
     * @return $this
     */
    public function setValidUntil(\DateTime $validUntil)
    {
        $validUntilTimestamp = $validUntil->getTimestamp();
        $this->set_default_property(self::PROPERTY_VALID_UNTIL, $validUntilTimestamp);
        return $this;
    }

    public static function get_table_name()
    {
        return 'user_invite';
    }

}