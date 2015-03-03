<?php
namespace Chamilo\Core\Metadata\Attribute\Ajax;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\Attribute\Storage\DataManager;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Gets all attributes that can be assigned to a specific element.
 * 
 * @author Tom Goethals
 */
class GetElementAttributesComponent extends \Chamilo\Core\Metadata\Attribute\Manager
{
    const PARAM_ELEMENT = 'element';
    const PARAM_NAMESPACE = 'namespace';
    const PROPERTY_ATTRIBUTES = 'attributes';

    /**
     *
     * @return array
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_ELEMENT, self :: PARAM_NAMESPACE);
    }

    function run()
    {
        $result = new JsonAjaxResult();
        $result->set_property(self :: PROPERTY_ATTRIBUTES, $this->get_attributes());
        $result->display();
    }

    /**
     * Get the attributes for the passed element id.
     * 
     * @return array
     */
    private function get_attributes()
    {
        $element = $this->getPostDataValue(self :: PARAM_ELEMENT);
        $namespace = $this->getPostDataValue(self :: PARAM_NAMESPACE);
        
        $element_obj = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_element_by_schema_namespace_and_element_name(
            $namespace, 
            $element);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_obj->get_id()));
        $element_rel_attributes = \Chamilo\Core\Metadata\Element\Storage\DataManager :: retrieves(
            ElementRelAttribute :: class_name(), 
            $condition);
        
        $json = array();
        while ($element_rel_attribute = $element_rel_attributes->next_result())
        {
            $attribute = DataManager :: retrieve_by_id(
                Attribute :: class_name(), 
                $element_rel_attribute->get_attribute_id());
            $json[] = array(
                Attribute :: PROPERTY_ID => $attribute->get_id(), 
                Attribute :: PROPERTY_NAME => $attribute->get_name());
        }
        
        return $json;
    }
}