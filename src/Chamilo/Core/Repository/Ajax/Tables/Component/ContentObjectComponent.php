<?php
namespace Chamilo\Core\Repository\Ajax\Tables\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Storage\Parameters\DataClassTableParametersConverter;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Architecture\JsonDataClassTableResponse;
use Chamilo\Core\Repository\Ajax\Tables\Service\ContentObjectTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\Tables\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class ContentObjectComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_CURRENT_PAGE = 'currentPage';
    const PARAM_ITEMS_PER_PAGE = 'itemsPerPage';
    const PARAM_ORDER_BY_PROPERTY = 'orderBy';
    const PARAM_ORDER_BY_DIRECTION = 'orderByReverse';
    const PARAM_GLOBAL_FILTER = 'globalFilter';
    const PARAM_INDIVIDUAL_FILTERS = 'individualFilters';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array(
            self::PARAM_CURRENT_PAGE,
            self::PARAM_ITEMS_PER_PAGE,
            self::PARAM_ORDER_BY_PROPERTY,
            self::PARAM_ORDER_BY_DIRECTION,
            self::PARAM_GLOBAL_FILTER);
    }

    /**
     *
     * @return integer
     */
    protected function getCurrentPage()
    {
        return (int) $this->getPostDataValue(self::PARAM_CURRENT_PAGE);
    }

    /**
     *
     * @return integer
     */
    protected function getItemsPerPage()
    {
        return (int) $this->getPostDataValue(self::PARAM_ITEMS_PER_PAGE);
    }

    /**
     *
     * @return string
     */
    protected function getGlobalFilter()
    {
        return (string) $this->getPostDataValue(self::PARAM_GLOBAL_FILTER);
    }

    protected function getGlobalFilterProperties()
    {
        return array(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
    }

    /**
     *
     * @return string[]
     */
    protected function getIndividualFilters()
    {
        return $this->getRequest()->getFromPost(self::PARAM_INDIVIDUAL_FILTERS);
    }

    /**
     *
     * @return string
     */
    protected function getOrderByProperty()
    {
        return (string) $this->getPostDataValue(self::PARAM_ORDER_BY_PROPERTY);
    }

    /**
     *
     * @return boolean
     */
    protected function getIsReverseOrder()
    {
        return (boolean) $this->getPostDataValue(self::PARAM_ORDER_BY_DIRECTION);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $tableDataProvider = $this->getTableDataProvider();

        $jsonResponse = new JsonDataClassTableResponse(
            $tableDataProvider->getTableRowData(),
            $tableDataProvider->getTableRowCount());
        $jsonResponse->send();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    protected function getContentObjectService()
    {
        return $this->getService('chamilo.core.repository.workspace.service.content_object_service');
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace
     */
    protected function getWorkspaceImplementation()
    {
        return new PersonalWorkspace($this->getUser());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    protected function getDataClassRetrievesParameters()
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
     * @return \Chamilo\Core\Repository\Ajax\Tables\Service\ContentObjectTableDataProvider
     */
    protected function getTableDataProvider()
    {
        return new ContentObjectTableDataProvider(
            $this->getDataClassRetrievesParameters(),
            $this->getContentObjectService(),
            $this->getWorkspaceImplementation());
    }
}
