<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package repository.content_object.assessment;
 */
class HintComponent extends \Chamilo\Core\Repository\ContentObject\Assessment\Ajax\Manager
{
    const PARAM_HINT_IDENTIFIER = 'hint_identifier';
    const PROPERTY_HINT = 'hint';
    const PROPERTY_ELEMENT_NAME = 'element_name';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_HINT_IDENTIFIER);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $identifiers = explode('_', $this->getPostDataValue(self :: PARAM_HINT_IDENTIFIER));
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
            $identifiers[0]);
        self :: factory($complex_content_object_item)->run();
    }

    public function factory($complex_content_object_item)
    {
        $context = $complex_content_object_item->get_ref_object()->context();
        $class = $context . '\HintComponent';
        
        if (! class_exists($class))
        {
            JsonAjaxResult :: bad_request();
        }
        
        $component = new $class($this->get_user());
        $component->set_complex_content_object_item($complex_content_object_item);
        return $component;
    }
}
