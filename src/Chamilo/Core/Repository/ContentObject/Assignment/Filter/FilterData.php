<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Filter;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;

/**
 * The data set via Session, $_POST and $_GET variables related to filtering a set of content objects
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilterData extends \Chamilo\Core\Repository\Filter\FilterData
{
    // Available type filters
    const FILTER_START_TIME = Assignment::PROPERTY_START_TIME;
    const FILTER_END_TIME = Assignment::PROPERTY_END_TIME;

    /**
     * Determine whether one or more of the parameters were set
     *
     * @return boolean
     */
    public function is_set()
    {
        return parent::is_set() || $this->has_date(self::FILTER_START_TIME) || $this->has_date(self::FILTER_END_TIME);
    }

    /**
     *
     * @param string[] $filter_properties
     *
     * @return string[]
     */
    public function get_filter_properties($filter_properties = array())
    {
        $filter_properties[] = self::FILTER_START_TIME;
        $filter_properties[] = self::FILTER_END_TIME;

        return parent::get_filter_properties($filter_properties);
    }

    /**
     *
     * @return boolean
     */
    public function has_start_time()
    {
        return $this->has_date(self::FILTER_START_TIME);
    }

    /**
     *
     * @return boolean
     */
    public function has_end_time()
    {
        return $this->has_date(self::FILTER_END_TIME);
    }

    /**
     *
     * @param string $type
     *
     * @return int NULL
     */
    public function get_start_time($type = self :: FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_START_TIME, $type);
    }

    /**
     *
     * @param string $type
     *
     * @return int NULL
     */
    public function get_end_time($type = self :: FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_END_TIME, $type);
    }
}