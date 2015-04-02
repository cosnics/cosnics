<?php
namespace Chamilo\Core\Metadata\Element\Ajax\Component;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Get all elements for a specific schema.
 * 
 * @author Tom Goethals
 */
class GetSchemaElementsComponent extends \Chamilo\Core\Metadata\Element\Ajax\Manager
{
    const PARAM_SCHEMA_ID = 'schema_id';
    const PROPERTY_ELEMENTS = 'elements';

    /**
     *
     * @return array
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_SCHEMA_ID);
    }

    function run()
    {
        $result = new JsonAjaxResult();
        $result->set_property(self :: PROPERTY_ELEMENTS, $this->get_elements());
        $result->display();
    }

    /**
     * Get the elements for the passed schema id.
     * 
     * @return array
     */
    private function get_elements()
    {
        $sid = $this->getPostDataValue(self :: PARAM_SCHEMA_ID);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($sid));
        $elements = DataManager :: retrieves(Element :: class_name(), $condition);
        
        $json = array();
        while ($element = $elements->next_result())
        {
            $json[] = array(
                Element :: PROPERTY_ID => $element->get_id(), 
                Element :: PROPERTY_NAME => $element->get_name(), 
                Element :: PROPERTY_SCHEMA_ID => $element->get_schema_id());
        }
        return $json;
    }
}