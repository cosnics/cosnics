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
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_USER_ID, 
                self::PROPERTY_CALENDAR_TYPE, 
                self::PROPERTY_CALENDAR_ID, 
                self::PROPERTY_AVAILABILITY, 
                self::PROPERTY_COLOUR));
    }

    public function get_data_manager()
    {
        return DataManager::getInstance();
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
    public function getCalendarType()
    {
        return $this->get_default_property(self::PROPERTY_CALENDAR_TYPE);
    }

    /**
     *
     * @param string $calendarType
     */
    public function setCalendarType($calendarType)
    {
        $this->set_default_property(self::PROPERTY_CALENDAR_TYPE, $calendarType);
    }

    /**
     *
     * @return string
     */
    public function getCalendarId()
    {
        return $this->get_default_property(self::PROPERTY_CALENDAR_ID);
    }

    /**
     *
     * @param string $calendarId
     */
    public function setCalendarId($calendarId)
    {
        $this->set_default_property(self::PROPERTY_CALENDAR_ID, $calendarId);
    }

    /**
     *
     * @return integer
     */
    public function getAvailability()
    {
        return $this->get_default_property(self::PROPERTY_AVAILABILITY);
    }

    /**
     *
     * @param integer $availability
     */
    public function setAvailability($availability)
    {
        $this->set_default_property(self::PROPERTY_AVAILABILITY, $availability);
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
        return $this->get_default_property(self::PROPERTY_COLOUR);
    }

    /**
     *
     * @param string $colour
     */
    public function setColour($colour)
    {
        $this->set_default_property(self::PROPERTY_COLOUR, $colour);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'calendar_availability';
    }
}
