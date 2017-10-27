<?php
namespace Chamilo\Core\Repository\Ajax\Tables\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class ContentObjectComponent extends \Chamilo\Core\Repository\Ajax\Manager
{

    const PROPERTY_CONTENT_OBJECT_DATA = 'content_object_data';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array();
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $filterData = $this->getFilterData();
        $workspaceImplementation = $this->getWorkspaceImplementation();
        
        $contentObjects = $this->getContentObjectService()->getContentObjectsByTypeForWorkspace($filterData->getTypeDataClass(), $workspaceImplementation, ConditionFilterRenderer::factory($filterData, $workspaceImplementation));
        
        $contentObjectData = array();
        
        while ($contentObject = $contentObjects->next_result()) {
            $contentObjectData[] = array(
                ContentObject::PROPERTY_TITLE => $contentObject->get_title(),
                ContentObject::PROPERTY_DESCRIPTION => $contentObject->get_description(),
                ContentObject::PROPERTY_MODIFICATION_DATE => $contentObject->get_modification_date()
            );
        }
        
        $properties = array(
            self::PROPERTY_CONTENT_OBJECT_DATA => $contentObjectData
        );
        
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
