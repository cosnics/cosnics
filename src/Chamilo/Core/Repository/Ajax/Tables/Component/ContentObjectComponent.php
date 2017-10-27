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
use Chamilo\Libraries\Storage\Parameters\DataClassTableParametersConverter;

class ContentObjectComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_CURRENT_PAGE = 'currentPage';
    const PARAM_ITEMS_PER_PAGE = 'itemsPerPage';
    const PARAM_ORDER_BY_PROPERTY = 'orderBy';
    const PARAM_ORDER_BY_DIRECTION = 'orderByReverse';
    const PARAM_GLOBAL_FILTER = 'globalFilter';
    const PARAM_INDIVIDUAL_FILTERS = 'individualFilters';
    const PROPERTY_CONTENT_OBJECT_DATA = 'content_object_data';
    const PROPERTY_CONTENT_OBJECT_COUNT = 'content_object_count';

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
        $dataClassTableParametersConverter = new DataClassTableParametersConverter();
        
        $dataClassRetrievesParameters = $dataClassTableParametersConverter->buildDataClassRetrievesParameters(
            $this->getCurrentPage(), 
            $this->getItemsPerPage(), 
            $this->getGlobalFilter(), 
            $this->getGlobalFilterProperties(), 
            $this->getIndividualFilters(), 
            $this->getOrderByProperty(), 
            $this->getIsReverseOrder());
        
        $workspaceImplementation = $this->getWorkspaceImplementation();
        
        $contentObjects = $this->getContentObjectService()->getContentObjectsByTypeForWorkspace(
            ContentObject::class, 
            $workspaceImplementation, 
            $dataClassRetrievesParameters->getCondition(), 
            $dataClassRetrievesParameters->getCount(), 
            $dataClassRetrievesParameters->getOffset(), 
            $dataClassRetrievesParameters->getOrderBy());
        
        $contentObjectData = array();
        
        $propertyPrefix = str_replace('\\', '_', ContentObject::class) . ':';
        
        while ($contentObject = $contentObjects->next_result())
        {
            $contentObjectData[] = array(
                $propertyPrefix . ContentObject::PROPERTY_TITLE => $contentObject->get_title(), 
                $propertyPrefix . ContentObject::PROPERTY_DESCRIPTION => $contentObject->get_description(), 
                $propertyPrefix . ContentObject::PROPERTY_MODIFICATION_DATE => $contentObject->get_modification_date());
        }
        
        $contentObjectCount = $this->getContentObjectService()->countContentObjectsByTypeForWorkspace(
            ContentObject::class, 
            $workspaceImplementation, 
            $dataClassRetrievesParameters->getCondition());
        
        $properties = array(
            self::PROPERTY_CONTENT_OBJECT_DATA => $contentObjectData, 
            self::PROPERTY_CONTENT_OBJECT_COUNT => $contentObjectCount);
        
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
}
