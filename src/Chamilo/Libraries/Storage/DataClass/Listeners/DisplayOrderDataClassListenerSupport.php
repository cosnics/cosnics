<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

/**
 * Interface that makes sure that dataclasses who use the display order data class listener also support the correct
 * functionality
 *
 * @package Chamilo\Libraries\Storage\DataClass\Listeners
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface DisplayOrderDataClassListenerSupport
{

    /**
     * @return mixed
     */
    public function getDefaultProperty(string $name);

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function get_display_order_context_properties();

    /**
     * Returns the property for the display order
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    public function get_display_order_property();

    /**
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function package();

    /**
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function setDefaultProperty(string $name, $value);
}
