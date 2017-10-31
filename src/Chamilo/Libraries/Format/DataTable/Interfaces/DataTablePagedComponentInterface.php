<?php
namespace Chamilo\Libraries\Format\DataTable\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
interface DataTablePagedComponentInterface
{
    const PARAM_CURRENT_PAGE = 'currentPage';
    const PARAM_ITEMS_PER_PAGE = 'itemsPerPage';
    const PARAM_ORDER_BY_PROPERTY = 'orderBy';
    const PARAM_ORDER_BY_DIRECTION = 'orderByReverse';
    const PARAM_GLOBAL_FILTER = 'globalFilter';
    const PARAM_INDIVIDUAL_FILTERS = 'individualFilters';

    /**
     *
     * @return integer
     */
    public function getCurrentPage();

    /**
     *
     * @return integer
     */
    public function getItemsPerPage();

    /**
     *
     * @return string
     */
    public function getGlobalFilter();

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getGlobalFilterProperties();

    /**
     *
     * @return string[]
     */
    public function getIndividualFilters();

    /**
     *
     * @return string
     */
    public function getOrderByProperty();

    /**
     *
     * @return boolean
     */
    public function getIsReverseOrder();

    /**
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    public function getDataClassRetrievesParameters();

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface
     */
    public function getDataTableProvider();
}

