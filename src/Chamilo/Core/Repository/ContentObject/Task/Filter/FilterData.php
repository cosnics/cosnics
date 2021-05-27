<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Filter;

use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;

/**
 * The data set via Session, $_POST and $_GET variables related to filtering a set of content objects
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilterData extends \Chamilo\Core\Repository\Filter\FilterData
{
    // Available type filters
    const FILTER_START_DATE = Task::PROPERTY_START_DATE;
    const FILTER_DUE_DATE = Task::PROPERTY_DUE_DATE;
    const FILTER_FREQUENCY = Task::PROPERTY_FREQUENCY;
    const FILTER_CATEGORY = Task::PROPERTY_CATEGORY;
    const FILTER_PRIORITY = Task::PROPERTY_PRIORITY;

    /**
     * Determine whether one or more of the parameters were set
     * 
     * @return boolean
     */
    public function is_set()
    {
        return parent::is_set() || $this->has_filter_property(self::FILTER_FREQUENCY) ||
             $this->has_date(self::FILTER_START_DATE) || $this->has_date(self::FILTER_DUE_DATE) ||
             $this->has_date(self::FILTER_CATEGORY) || $this->has_date(self::FILTER_PRIORITY);
    }

    /**
     *
     * @param string[] $filter_properties
     * @return string[]
     */
    public function get_filter_properties($filter_properties = [])
    {
        $filter_properties[] = self::FILTER_START_DATE;
        $filter_properties[] = self::FILTER_DUE_DATE;
        $filter_properties[] = self::FILTER_FREQUENCY;
        $filter_properties[] = self::FILTER_CATEGORY;
        $filter_properties[] = self::FILTER_PRIORITY;
        
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
    public function has_due_date()
    {
        return $this->has_date(self::FILTER_DUE_DATE);
    }

    /**
     *
     * @param string $type
     * @return int NULL
     */
    public function get_start_date($type = self::FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_START_DATE, $type);
    }

    /**
     *
     * @param string $type
     * @return int NULL
     */
    public function get_due_date($type = self::FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_DUE_DATE, $type);
    }
}