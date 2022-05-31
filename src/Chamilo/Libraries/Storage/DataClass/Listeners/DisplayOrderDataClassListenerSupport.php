<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

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
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getDisplayOrderContextProperties(): array;

    public function getDisplayOrderProperty(): PropertyConditionVariable;

    /**
     * @throws \ReflectionException
     */
    public static function package(): string;

    /**
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function setDefaultProperty(string $name, $value);
}
