<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Filter;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;

/**
 * The data set via Session, $_POST and $_GET variables related to filtering a set of content objects
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilterData extends \Chamilo\Core\Repository\Filter\FilterData
{
    // Available type filters
    const FILTER_FILESIZE = Webpage :: PROPERTY_FILESIZE;
    const FILTER_COMPARE = 'compare';
    const FILTER_FORMAT = 'format';

    /**
     * Determine whether one or more of the parameters were set
     * 
     * @return boolean
     */
    public function is_set()
    {
        return parent :: is_set() || $this->has_filter_property(self :: FILTER_FILESIZE);
    }

    /**
     *
     * @param string[] $filter_properties
     * @return string[]
     */
    public function get_filter_properties($filter_properties = array())
    {
        $filter_properties[] = self :: FILTER_FILESIZE;
        $filter_properties[] = self :: FILTER_COMPARE;
        $filter_properties[] = self :: FILTER_FORMAT;
        
        return parent :: get_filter_properties($filter_properties);
    }
}