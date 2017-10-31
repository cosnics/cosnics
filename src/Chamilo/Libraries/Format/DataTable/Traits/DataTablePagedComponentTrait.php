<?php
namespace Chamilo\Libraries\Format\DataTable\Traits;

use Chamilo\Libraries\Format\DataTable\Interfaces\DataTablePagedComponentInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassTableParametersConverter;
use Chamilo\Libraries\Architecture\JsonDataClassTableResponse;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Traits
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataTablePagedComponentTrait
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array(
            DataTablePagedComponentInterface::PARAM_CURRENT_PAGE,
            DataTablePagedComponentInterface::PARAM_ITEMS_PER_PAGE,
            DataTablePagedComponentInterface::PARAM_ORDER_BY_PROPERTY,
            DataTablePagedComponentInterface::PARAM_ORDER_BY_DIRECTION,
            DataTablePagedComponentInterface::PARAM_GLOBAL_FILTER);
    }

    /**
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return (int) $this->getPostDataValue(DataTablePagedComponentInterface::PARAM_CURRENT_PAGE);
    }

    /**
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        return (int) $this->getPostDataValue(DataTablePagedComponentInterface::PARAM_ITEMS_PER_PAGE);
    }

    /**
     *
     * @return string
     */
    public function getGlobalFilter()
    {
        return (string) $this->getPostDataValue(DataTablePagedComponentInterface::PARAM_GLOBAL_FILTER);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    abstract public function getGlobalFilterProperties();

    /**
     *
     * @return string[]
     */
    public function getIndividualFilters()
    {
        return $this->getRequest()->getFromPost(DataTablePagedComponentInterface::PARAM_INDIVIDUAL_FILTERS);
    }

    /**
     *
     * @return string
     */
    public function getOrderByProperty()
    {
        return (string) $this->getPostDataValue(DataTablePagedComponentInterface::PARAM_ORDER_BY_PROPERTY);
    }

    /**
     *
     * @return boolean
     */
    public function getIsReverseOrder()
    {
        return (boolean) $this->getPostDataValue(DataTablePagedComponentInterface::PARAM_ORDER_BY_DIRECTION);
    }

    /**
     * Returns the value of the given parameter.
     *
     * @param string $name
     * @return string|string[]
     */
    abstract public function getPostDataValue($name);

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    abstract public function getRequest();

    /**
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    public function getDataClassRetrievesParameters()
    {
        $dataClassTableParametersConverter = new DataClassTableParametersConverter();

        return $dataClassTableParametersConverter->buildDataClassRetrievesParameters(
            $this->getCurrentPage(),
            $this->getItemsPerPage(),
            $this->getGlobalFilter(),
            $this->getGlobalFilterProperties(),
            $this->getIndividualFilters(),
            $this->getOrderByProperty(),
            $this->getIsReverseOrder());
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface
     */
    abstract public function getDataTableProvider();

    public function run()
    {
        $tableDataProvider = $this->getDataTableProvider();

        $jsonResponse = new JsonDataClassTableResponse(
            $tableDataProvider->getDataTableRowData(),
            $tableDataProvider->getDataTableRowCount());
        $jsonResponse->send();
    }
}

