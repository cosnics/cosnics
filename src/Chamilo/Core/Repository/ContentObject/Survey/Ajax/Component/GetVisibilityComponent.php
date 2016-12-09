<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package repository.content_object.survey;
 */
class GetVisibilityComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Ajax\Manager
{

    function run()
    {
        $result = new JsonAjaxResult(200);
        $nodeId = $this->getRequest()->request->get(self :: PARAM_NODE_ID);
        $contentObjectId = $this->getRequest()->request->get(self :: PARAM_CONTENT_OBJECT_ID);
        
        $contentObject = DataManager :: retrieve_by_id(Survey :: class_name(), $contentObjectId);
        
        $path = $contentObject->get_complex_content_object_path();
        
        $node = $path->get_node($nodeId);
        
        $nodeVisibility = $node->getSiblingVisibility($this->getApplicationConfiguration()->getAnswerService());
               
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PARAM_QUESTION_VISIBILITY, $nodeVisibility);
        $result->display();
    }
}
?>