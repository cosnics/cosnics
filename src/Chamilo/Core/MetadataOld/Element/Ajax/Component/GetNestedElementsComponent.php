<?php
namespace Chamilo\Core\MetadataOld\Element\Ajax\Component;

use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\MetadataOld\Element\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Gets the elements nested within a specific element.
 * 
 * @author Tom Goethals
 */
class GetNestedElementsComponent extends \Chamilo\Core\MetadataOld\Element\Ajax\Manager
{
    const PARAM_ELEMENT_ID = 'element_id';
    const PROPERTY_ELEMENTS = 'elements';

    /**
     *
     * @return array
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_ELEMENT_ID);
    }

    function run()
    {
        $result = new JsonAjaxResult();
        $result->set_property(self :: PROPERTY_ELEMENTS, $this->get_elements());
        $result->display();
    }

    /**
     * Get the nested elements for the passed element id.
     * 
     * @return array
     */
    private function get_elements()
    {
        $eid = $this->getPostDataValue(self :: PARAM_ELEMENT_ID);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            new StaticConditionVariable($eid));
        
        $nestings = DataManager :: retrieves(ElementNesting :: class_name(), $condition);
        
        $json = array();
        while ($nesting = $nestings->next_result())
        {
            $element = DataManager :: retrieve_by_id(Element :: class_name(), $nesting->get_child_element_id());
            $json[] = array(
                Element :: PROPERTY_ID => $element->get_id(), 
                Element :: PROPERTY_NAME => $element->get_name(), 
                Element :: PROPERTY_SCHEMA_ID => $element->get_schema_id());
        }
        
        return $json;
    }
}