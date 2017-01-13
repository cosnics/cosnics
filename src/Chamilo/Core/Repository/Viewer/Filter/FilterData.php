<?php
namespace Chamilo\Core\Repository\Viewer\Filter;

/**
 * Custom filter data class for repository viewer
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterData extends \Chamilo\Core\Repository\Filter\FilterData
{
    const STORAGE = 'repo_viewer_filter';

    const FILTER_EXCLUDED_CONTENT_OBJECT_IDS = 'excluded_content_object_ids';

    /**
     * Returns the value of a filter property from a request
     * 
     * @param string $filterProperty
     *
     * @return string
     */
    protected function getFromRequest($filterProperty)
    {
        if ($filterProperty == self::FILTER_CATEGORY)
        {
            $filterProperty = 'category';
        }
        
        return parent::getFromRequest($filterProperty);
    }

    /**
     * @return int[]
     */
    public function getExcludedContentObjectIds()
    {
        return $this->get_filter_property(self::FILTER_EXCLUDED_CONTENT_OBJECT_IDS);
    }

    /**
     * @param int[] $excludedContentObjectIds
     */
    public function setExcludedContentObjectIds($excludedContentObjectIds = array())
    {
        if(!is_array($excludedContentObjectIds))
        {
            throw new \InvalidArgumentException('The given argument $excludedContentObjectIds should be a valid array');
        }

        $this->set_filter_property(self::FILTER_EXCLUDED_CONTENT_OBJECT_IDS, $excludedContentObjectIds);
    }
}