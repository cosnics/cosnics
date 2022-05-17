<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
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
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_USER_ID = 'user_id';

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @return string
     */
    public function getSource()
    {
        return $this->getDefaultProperty(self::PROPERTY_SOURCE);
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        if (isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(User::class, (int) $this->getUserId());
        }

        return $this->user;
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * Get the default properties of a Visibility DataClass
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_SOURCE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @param $source
     *
     * @throws \Exception
     */
    public function setSource($source)
    {
        $this->setDefaultProperty(self::PROPERTY_SOURCE, $source);
    }

    /**
     * @param $id
     *
     * @throws \Exception
     */
    public function setUserId($id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $id);
    }
}
