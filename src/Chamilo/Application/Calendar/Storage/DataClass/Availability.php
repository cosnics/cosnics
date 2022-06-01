<?php
namespace Chamilo\Application\Calendar\Storage\DataClass;

use Chamilo\Application\Calendar\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Application\Calendar\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Availability extends DataClass
{
    
    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CALENDAR_TYPE = 'calendar_type';
    const PROPERTY_CALENDAR_ID = 'calendar_id';
    const PROPERTY_AVAILABILITY = 'availability';
    const PROPERTY_COLOUR = 'colour';

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_USER_ID, 
                self::PROPERTY_CALENDAR_TYPE, 
                self::PROPERTY_CALENDAR_ID, 
                self::PROPERTY_AVAILABILITY, 
                self::PROPERTY_COLOUR));
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
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);
    }

    /**
     *
     * @return string
     */
    public function getCalendarType()
    {
        return $this->getDefaultProperty(self::PROPERTY_CALENDAR_TYPE);
    }

    /**
     *
     * @param string $calendarType
     */
    public function setCalendarType($calendarType)
    {
        $this->setDefaultProperty(self::PROPERTY_CALENDAR_TYPE, $calendarType);
    }

    /**
     *
     * @return string
     */
    public function getCalendarId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CALENDAR_ID);
    }

    /**
     *
     * @param string $calendarId
     */
    public function setCalendarId($calendarId)
    {
        $this->setDefaultProperty(self::PROPERTY_CALENDAR_ID, $calendarId);
    }

    /**
     *
     * @return integer
     */
    public function getAvailability()
    {
        return $this->getDefaultProperty(self::PROPERTY_AVAILABILITY);
    }

    /**
     *
     * @param integer $availability
     */
    public function setAvailability($availability)
    {
        $this->setDefaultProperty(self::PROPERTY_AVAILABILITY, $availability);
    }

    /**
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getAvailability() == true;
    }

    /**
     *
     * @return boolean
     */
    public function isInactive()
    {
        return $this->getAvailability() == false;
    }

    /**
     *
     * @return string
     */
    public function getColour()
    {
        return $this->getDefaultProperty(self::PROPERTY_COLOUR);
    }

    /**
     *
     * @param string $colour
     */
    public function setColour($colour)
    {
        $this->setDefaultProperty(self::PROPERTY_COLOUR, $colour);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'calendar_availability';
    }
}
