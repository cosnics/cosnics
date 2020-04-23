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
     * Gets a default property of this data class object by name.
     *
     * @param string $name The name of the property
     *
     * @return mixed
     */
    public function getDefaultProperty($name);

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
     * Sets a default property of this data class by name.
     *
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     *
     * @throws \Exception
     */
    public function setDefaultProperty($name, $value);
}
