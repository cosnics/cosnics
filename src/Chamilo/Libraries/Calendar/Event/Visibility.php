<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Visibility extends DataClass
{

    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SOURCE = 'source';

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     * Get the default properties of a Visibility DataClass
     *
     * @param string[] $extendedPropertyNames
     * @return string[] The property names.
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_SOURCE;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @return string
     */
    public function getSource()
    {
        return $this->get_default_property(self::PROPERTY_SOURCE);
    }

    /**
     *
     * @param integer $id
     */
    public function setUserId($id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $id);
    }

    /**
     *
     * @param string $source
     */
    public function setSource($source)
    {
        $this->set_default_property(self::PROPERTY_SOURCE, $source);
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        if (isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) $this->getUserId());
        }

        return $this->user;
    }
}
