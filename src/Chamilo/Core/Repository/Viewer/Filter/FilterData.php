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
}