<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Calendar\Event\RecurringContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 */
class CalendarEvent extends RecurringContentObject implements Versionable, AttachmentSupportInterface, Includeable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\CalendarEvent';

    public const PROPERTY_END_DATE = 'end_date';
    public const PROPERTY_LOCATION = 'location';
    public const PROPERTY_START_DATE = 'start_date';

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return [
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
        ];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_calendar_event';
    }

    /**
     * @return int
     */
    public function get_end_date()
    {
        return $this->getAdditionalProperty(self::PROPERTY_END_DATE);
    }

    /**
     * @return string
     */
    public function get_location()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LOCATION);
    }

    /**
     * @return int
     */
    public function get_start_date()
    {
        return $this->getAdditionalProperty(self::PROPERTY_START_DATE);
    }

    /**
     * @return string
     */
    public function get_type_string(): string
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

    /**
     * @return string
     */
    public static function parentClassName(): string
    {
        return ContentObject::class;
    }

    /**
     * @param int $end_date
     */
    public function set_end_date($end_date)
    {
        return $this->setAdditionalProperty(self::PROPERTY_END_DATE, $end_date);
    }

    /**
     * @param $location
     */
    public function set_location($location)
    {
        return $this->setAdditionalProperty(self::PROPERTY_LOCATION, $location);
    }

    /**
     * @param int $start_date
     */
    public function set_start_date($start_date)
    {
        return $this->setAdditionalProperty(self::PROPERTY_START_DATE, $start_date);
    }
}
