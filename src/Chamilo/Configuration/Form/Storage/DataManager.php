<?php
namespace Chamilo\Configuration\Form\Storage;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataClass\Option;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;

/**
 * @package configuration\form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\Repository\DataManager
{
    public const PREFIX = 'configuration_form_';

    public static function retrieve_dynamic_form_element_options(
        $condition = null, $offset = null, $count = null, $order_property = null
    )
    {
        $parameters = new StorageParameters(
            condition: $condition, orderBy: $order_property, count: $count, offset: $offset
        );

        return self::retrieves(Option::class, $parameters);
    }

    public static function retrieve_dynamic_form_elements(
        $condition = null, $offset = null, $count = null, ?OrderBy $order_property = new OrderBy()
    )
    {
        $parameters = new StorageParameters(
            condition: $condition, orderBy: $order_property, count: $count, offset: $offset
        );

        return self::retrieves(Element::class, $parameters);
    }

    public static function select_next_dynamic_form_element_option_order($dynamic_form_element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class, Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new StaticConditionVariable($dynamic_form_element_id)
        );

        return self::retrieve_next_value(Option::class, Option::PROPERTY_DISPLAY_ORDER, $condition);
    }

    public static function select_next_dynamic_form_element_order($dynamic_form_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($dynamic_form_id)
        );

        return self::retrieve_next_value(Element::class, Element::PROPERTY_DISPLAY_ORDER, $condition);
    }
}
