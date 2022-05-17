<?php
namespace Chamilo\Configuration\Form\Storage;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataClass\Option;
use Chamilo\Configuration\Form\Storage\DataClass\Value;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'configuration_form_';

    public static function delete_all_options_from_form_element($dynamic_form_element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class, Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new StaticConditionVariable($dynamic_form_element_id));
        return self::deletes(Option::class, $condition);
    }

    public static function delete_dynamic_form_element_values_from_form($dynamic_form_id)
    {
        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($dynamic_form_id));
        $subselect = new SubselectCondition(
            new PropertyConditionVariable(Value::class, Value::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new PropertyConditionVariable(Element::class, Element::PROPERTY_ID),
            Element::getTableName(),
            $subcondition);

        return self::deletes(Value::class, $subselect);
    }

    public static function retrieve_dynamic_form_element_values($condition = null, $offset = null, $count = null,
        $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return self::retrieves(Value::class, $parameters);
    }

    public static function retrieve_dynamic_form_elements($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return self::retrieves(Element::class, $parameters);
    }

    public static function select_next_dynamic_form_element_option_order($dynamic_form_element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class, Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new StaticConditionVariable($dynamic_form_element_id));
        return self::retrieve_next_value(Option::class, Option::PROPERTY_DISPLAY_ORDER, $condition);
    }

    public static function retrieve_dynamic_form_element_options($condition = null, $offset = null, $count = null,
        $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return self::retrieves(Option::class, $parameters);
    }

    public static function select_next_dynamic_form_element_order($dynamic_form_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($dynamic_form_id));
        return self::retrieve_next_value(Element::class, Element::PROPERTY_DISPLAY_ORDER, $condition);
    }

    public static function retrieve_dynamic_forms($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return self::retrieves(Instance::class, $parameters);
    }

    public static function count_dynamic_form_elements($conditions = null)
    {
        $parameters = new DataClassCountParameters($conditions);
        return self::count(Element::class, $parameters);
    }

    public static function count_dynamic_form_element_options($conditions = null)
    {
        $parameters = new DataClassCountParameters($conditions);
        return self::count(Option::class, $parameters);
    }

    public static function count_dynamic_form_element_values($conditions = null)
    {
        $parameters = new DataClassCountParameters($conditions);
        return self::count(Value::class, $parameters);
    }
}
