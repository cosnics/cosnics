<?php
namespace Chamilo\Core\Repository\Ajax\Tables\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class ContentObjectComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_CURRENT_PAGE = 'currentPage';
    const PARAM_ITEMS_PER_PAGE = 'pageItems';
    const PARAM_ORDER_BY_PROPERTY = 'orderBy';
    const PARAM_ORDER_BY_DIRECTION = 'orderByReverse';
    const PARAM_FILTER = 'filter';
    const PARAM_FILTER_FIELDS = 'filterfields';
    const PROPERTY_CONTENT_OBJECT_DATA = 'content_object_data';

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
            self::PARAM_FILTER, 
            self::PARAM_FILTER_FIELDS);
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
     * @return integer
     */
    protected function getOffset()
    {
        return $this->getCurrentPage() * $this->getItemsPerPage();
    }

    protected function getOrderby()
    {
        $orderByReverse = (boolean) $this->getPostDataValue(self::PARAM_ORDER_BY_DIRECTION);
        $orderByPropertyParts = explode(':', $this->getPostDataValue(self::PARAM_ORDER_BY_PROPERTY));
        
        if (empty($orderByPropertyParts) || count($orderByPropertyParts) != 2)
        {
            throw new \InvalidArgumentException();
        }
        
        $orderByClassName = str_replace('_', '\\', $orderByPropertyParts[0]);
        
        if (! class_exists($orderByClassName) || ! is_subclass_of($orderByClassName, DataClass::class))
        {
            throw new \InvalidArgumentException();
        }
        
        return array(
            new OrderBy(
                new PropertyConditionVariable($orderByClassName, $orderByPropertyParts[1]), 
                $orderByReverse ? SORT_DESC : SORT_ASC));
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $filterData = $this->getFilterData();
        $workspaceImplementation = $this->getWorkspaceImplementation();
        
        $contentObjects = $this->getContentObjectService()->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), 
            $workspaceImplementation, 
            ConditionFilterRenderer::factory($filterData, $workspaceImplementation)->render(), 
            $this->getOffset(), 
            $this->getItemsPerPage(), 
            $this->getOrderBy());
        
        $contentObjectData = array();
        
        while ($contentObject = $contentObjects->next_result())
        {
            $contentObjectData[] = array(
                ContentObject::PROPERTY_TITLE => $contentObject->get_title(), 
                ContentObject::PROPERTY_DESCRIPTION => $contentObject->get_description(), 
                ContentObject::PROPERTY_MODIFICATION_DATE => $contentObject->get_modification_date());
        }
        
        $properties = array(self::PROPERTY_CONTENT_OBJECT_DATA => $contentObjectData);
        
        $jsonAjaxResult = new JsonAjaxResult();
        $jsonAjaxResult->set_properties($properties);
        $jsonAjaxResult->display();
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
     * @return \Chamilo\Core\Repository\Filter\FilterData
     */
    protected function getFilterData()
    {
        return FilterData::getInstance($this->getWorkspaceImplementation());
    }
}
