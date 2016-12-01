<?php
namespace Chamilo\Core\Repository\Filter;

use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;

/**
 * Extension on the ButtonSearchForm class to take the filter data session into account
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterDataButtonSearchForm extends ButtonSearchForm
{

    /**
     *
     * @var FilterData
     */
    protected $filterData;

    /**
     * Constructor
     * FilterDataButtonSearchForm constructor.
     * 
     * @param string $url
     * @param FilterData $filterData
     */
    public function __construct($url, FilterData $filterData)
    {
        $this->filterData = $filterData;
        parent::__construct($url);
    }

    /**
     * Returns the query based on the filter data
     */
    public function getQuery()
    {
        $query = parent::getQuery();
        
        if ($query)
        {
            return $query;
        }
        
        return $this->filterData->get_filter_property(FilterData::FILTER_TEXT);
    }

    /**
     * Checks if the clear form is submitted and removes the filter data
     * 
     * @return bool
     */
    public function clearFormSubmitted()
    {
        if (parent::clearFormSubmitted())
        {
            $this->filterData->set_filter_property(FilterData::FILTER_TEXT, null);
            return true;
        }
        
        return false;
    }
}