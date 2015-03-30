<?php
namespace Chamilo\Core\Metadata\Ajax\Component;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Metadata\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GetElementsComponent extends \Chamilo\Core\Metadata\Ajax\Manager
{

    public function run()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID),
            new StaticConditionVariable(Request :: post(Element :: PROPERTY_SCHEMA_ID)));

        // property types are retrieved according to display order
        $elements = DataManager :: retrieves(Element :: class_name(), $condition);

        $types = array();

        $metadata_element_values = $this->get_metadata_element_values_for_content_object(
            Request :: post('object_id'),
            Request :: post('object_type'));

        $focus_isset = false;

        while ($element = $elements->next_result())
        {
            $focus = false;

            // give focus to the first element in the display_order that isn't linked
            if (! array_key_exists($element->get_id(), $metadata_element_values) && $focus_isset == false)
            {
                $focus = true;
                $focus_isset = true;
            }
            $types[$element->get_id()] = array($element->get_name(), $focus);
        }

        echo json_encode($types);
    }

    /**
     * gets element values
     *
     * @param $object_id int
     * @param $object_type int content_object, user, group
     * @return Array $element_values[element_value_id] = true
     */
    function get_metadata_element_values_for_content_object($object_id, $object_type)
    {
        // get element_values linked to object
        $constant_string = (string) StringUtilities :: getInstance()->createString($object_type)->upperCamelize() .
             'MetadataElementValue::PROPERTY_' . strtoupper($object_type) . '_ID';
        // $id = \constant($constant_string); //?not working ?

        $condition = new EqualityCondition($object_type . '_id', $object_id);

        $class = '\Chamilo\Core\Metadata\\' . $object_type;

        $values = DataManager :: retrieves($class, $condition);

        while ($value = $values->next_result())
        {
            $element_values[$value->get_element_id()] = true;
        }

        return $element_values;
    }
}