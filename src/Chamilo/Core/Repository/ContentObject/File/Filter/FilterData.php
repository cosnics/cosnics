<?php
namespace Chamilo\Core\Repository\ContentObject\File\Filter;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;

/**
 * The data set via Session, $_POST and $_GET variables related to filtering a set of content objects
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilterData extends \Chamilo\Core\Repository\Filter\FilterData
{
    // Available type filters
    const FILTER_FILESIZE = File :: PROPERTY_FILESIZE;
    const FILTER_COMPARE = 'compare';
    const FILTER_FORMAT = 'format';
    const FILTER_EXTENSION = File :: PROPERTY_EXTENSION;
    const FILTER_EXTENSION_TYPE = 'extension_type';

    /**
     * Determine whether one or more of the parameters were set
     * 
     * @return boolean
     */
    public function is_set()
    {
        return parent :: is_set() || $this->has_filter_property(self :: FILTER_FILESIZE) ||
             $this->has_filter_property(self :: FILTER_EXTENSION_TYPE) ||
             $this->has_filter_property(self :: FILTER_EXTENSION);
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
        $filter_properties[] = self :: FILTER_EXTENSION_TYPE;
        $filter_properties[] = self :: FILTER_EXTENSION;
        
        return parent :: get_filter_properties($filter_properties);
    }
}