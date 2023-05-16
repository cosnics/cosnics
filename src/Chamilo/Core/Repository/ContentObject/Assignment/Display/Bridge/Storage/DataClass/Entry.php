<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Exception;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Entry extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    public const PROPERTY_ENTITY_ID = 'entity_id';

    public const PROPERTY_ENTITY_TYPE = 'entity_type';

    public const PROPERTY_IP_ADDRESS = 'ip_address';

    public const PROPERTY_SUBMITTED = 'submitted';

    public const PROPERTY_USER_ID = 'user_id';

    private $contentObject;

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        try
        {
            if (!isset($this->contentObject))
            {
                $this->contentObject = DataManager::retrieve_by_id(
                    ContentObject::class, $this->getContentObjectId()
                );
            }

            return $this->contentObject;
        }
        catch (Exception $ex)
        {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extendedPropertyNames[] = self::PROPERTY_SUBMITTED;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_IP_ADDRESS;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     * @return int
     */
    public function getEntityType()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->getDefaultProperty(self::PROPERTY_IP_ADDRESS);
    }

    /**
     * @return int
     */
    public function getSubmitted()
    {
        return $this->getDefaultProperty(self::PROPERTY_SUBMITTED);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @param int $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }

    /**
     * @param int $entityId
     */
    public function setEntityId($entityId)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entityId);
    }

    /**
     * @param int $entityType
     */
    public function setEntityType($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->setDefaultProperty(self::PROPERTY_IP_ADDRESS, $ipAddress);
    }

    /**
     * @param int $submitted
     */
    public function setSubmitted($submitted)
    {
        $this->setDefaultProperty(self::PROPERTY_SUBMITTED, $submitted);
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);
    }
}
