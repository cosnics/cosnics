<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Calendar\Event\Recurrence\RecurringContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class CalendarEvent extends RecurringContentObject implements Versionable, AttachmentSupport, Includeable
{
    const PROPERTY_END_DATE = 'end_date';
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_START_DATE = 'start_date';

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_LOCATION,
            self::PROPERTY_START_DATE,
            self::PROPERTY_END_DATE,
            self::PROPERTY_UNTIL,
            self::PROPERTY_FREQUENCY,
            self::PROPERTY_FREQUENCY_COUNT,
            self::PROPERTY_FREQUENCY_INTERVAL,
            self::PROPERTY_BYDAY,
            self::PROPERTY_BYMONTH,
            self::PROPERTY_BYMONTHDAY
        );
    }

    /**
     * Gets the end date of this calendar event
     *
     * @return int The end date
     */
    public function get_end_date()
    {
        return $this->get_additional_property(self::PROPERTY_END_DATE);
    }

    /**
     * Gets the start date of this calendar event
     *
     * @return int The start date
     */
    public function get_location()
    {
        return $this->get_additional_property(self::PROPERTY_LOCATION);
    }

    /**
     * Gets the start date of this calendar event
     *
     * @return int The start date
     */
    public function get_start_date()
    {
        return $this->get_additional_property(self::PROPERTY_START_DATE);
    }

    /**
     *
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function get_type_string()
    {
        if ($this->has_frequency())
        {
            return Translation::get('RepeatingCalendarEvent');
        }
        else
        {
            return parent::get_type_string();
        }
    }

    public static function parent_class_name()
    {
        return ContentObject::class;
    }

    /**
     * Sets the end date of this calendar event
     *
     * @param int The end date
     */
    public function set_end_date($end_date)
    {
        return $this->set_additional_property(self::PROPERTY_END_DATE, $end_date);
    }

    /**
     * Sets the start date of this calendar event
     *
     * @param string
     */
    public function set_location($location)
    {
        return $this->set_additional_property(self::PROPERTY_LOCATION, $location);
    }

    /**
     * Sets the start date of this calendar event
     *
     * @param int The start date
     */
    public function set_start_date($start_date)
    {
        return $this->set_additional_property(self::PROPERTY_START_DATE, $start_date);
    }
}
