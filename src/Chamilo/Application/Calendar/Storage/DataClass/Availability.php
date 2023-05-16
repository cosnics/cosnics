<?php
namespace Chamilo\Application\Calendar\Storage\DataClass;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Calendar\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Availability extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_AVAILABILITY = 'availability';
    public const PROPERTY_CALENDAR_ID = 'calendar_id';
    public const PROPERTY_CALENDAR_TYPE = 'calendar_type';
    public const PROPERTY_COLOUR = 'colour';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * @return int
     */
    public function getAvailability()
    {
        return $this->getDefaultProperty(self::PROPERTY_AVAILABILITY);
    }

    /**
     * @return string
     */
    public function getCalendarId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CALENDAR_ID);
    }

    /**
     * @return string
     */
    public function getCalendarType()
    {
        return $this->getDefaultProperty(self::PROPERTY_CALENDAR_TYPE);
    }

    /**
     * @return string
     */
    public function getColour()
    {
        return $this->getDefaultProperty(self::PROPERTY_COLOUR);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_CALENDAR_TYPE,
                self::PROPERTY_CALENDAR_ID,
                self::PROPERTY_AVAILABILITY,
                self::PROPERTY_COLOUR
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'calendar_availability';
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getAvailability() == true;
    }

    /**
     * @return bool
     */
    public function isInactive()
    {
        return $this->getAvailability() == false;
    }

    /**
     * @param int $availability
     */
    public function setAvailability($availability)
    {
        $this->setDefaultProperty(self::PROPERTY_AVAILABILITY, $availability);
    }

    /**
     * @param string $calendarId
     */
    public function setCalendarId($calendarId)
    {
        $this->setDefaultProperty(self::PROPERTY_CALENDAR_ID, $calendarId);
    }

    /**
     * @param string $calendarType
     */
    public function setCalendarType($calendarType)
    {
        $this->setDefaultProperty(self::PROPERTY_CALENDAR_TYPE, $calendarType);
    }

    /**
     * @param string $colour
     */
    public function setColour($colour)
    {
        $this->setDefaultProperty(self::PROPERTY_COLOUR, $colour);
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);
    }
}
