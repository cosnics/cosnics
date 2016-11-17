<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Filter;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;

/**
 * The data set via Session, $_POST and $_GET variables related to filtering a set of content objects
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilterData extends \Chamilo\Core\Repository\Filter\FilterData
{
    // Available type filters
    const FILTER_START_DATE = CalendarEvent::PROPERTY_START_DATE;
    const FILTER_END_DATE = CalendarEvent::PROPERTY_END_DATE;
    const FILTER_FREQUENCY = CalendarEvent::PROPERTY_FREQUENCY;

    /**
     * Determine whether one or more of the parameters were set
     * 
     * @return boolean
     */
    public function is_set()
    {
        return parent::is_set() || $this->has_filter_property(self::FILTER_FREQUENCY) ||
             $this->has_date(self::FILTER_START_DATE) || $this->has_date(self::FILTER_END_DATE);
    }

    /**
     *
     * @param string[] $filter_properties
     * @return string[]
     */
    public function get_filter_properties($filter_properties = array())
    {
        $filter_properties[] = self::FILTER_START_DATE;
        $filter_properties[] = self::FILTER_END_DATE;
        $filter_properties[] = self::FILTER_FREQUENCY;
        
        return parent::get_filter_properties($filter_properties);
    }

    /**
     *
     * @return boolean
     */
    public function has_start_date()
    {
        return $this->has_date(self::FILTER_START_DATE);
    }

    /**
     *
     * @return boolean
     */
    public function has_end_date()
    {
        return $this->has_date(self::FILTER_END_DATE);
    }

    /**
     *
     * @param string $type
     * @return int NULL
     */
    public function get_start_date($type = self :: FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_START_DATE, $type);
    }

    /**
     *
     * @param string $type
     * @return int NULL
     */
    public function get_end_date($type = self :: FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_END_DATE, $type);
    }
}