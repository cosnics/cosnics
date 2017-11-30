<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Entry extends DataClass
{
    // Properties
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_SUBMITTED = 'submitted';
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_IP_ADDRESS = 'ip_address';

    private $contentObject;

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extendedPropertyNames[] = self::PROPERTY_SUBMITTED;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_IP_ADDRESS;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     *
     * @return integer
     */
    public function getContentObjectId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param integer $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }

    /**
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @param integer $entityId
     */
    public function setEntityId($entityId)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entityId);
    }

    /**
     *
     * @return integer
     */
    public function getSubmitted()
    {
        return $this->get_default_property(self::PROPERTY_SUBMITTED);
    }

    /**
     *
     * @param integer $submitted
     */
    public function setSubmitted($submitted)
    {
        $this->set_default_property(self::PROPERTY_SUBMITTED, $submitted);
    }

    /**
     *
     * @return integer
     */
    public function getEntityType()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param integer $entityType
     */
    public function setEntityType($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
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
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
    }

    /**
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->get_default_property(self::PROPERTY_IP_ADDRESS);
    }

    /**
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->set_default_property(self::PROPERTY_IP_ADDRESS, $ipAddress);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        try
        {
            if (!isset($this->contentObject))
            {
                $this->contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $this->getContentObjectId()
                );
            }

            return $this->contentObject;
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }
}
