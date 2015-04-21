<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class ProcessOrderComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Ajax\Manager
{
    const PARAM_ID = 'id';
    const PARAM_ORDER = 'order';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_ID, self :: PARAM_ORDER);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $id = $this->getPostDataValue(self :: PARAM_ID);
        $order = $this->getPostDataValue(self :: PARAM_ORDER);
        
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
            $id);
        $complex_content_object_item->set_display_order($order);
        
        if ($complex_content_object_item->update())
        {
            $result = new JsonAjaxResult(200);
            $result->display();
        }
        else
        {
            $result = new JsonAjaxResult(500);
            $result->set_property(
                ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, 
                $complex_content_object_item->get_display_order());
            
            $result->display();
        }
    }
}
?>