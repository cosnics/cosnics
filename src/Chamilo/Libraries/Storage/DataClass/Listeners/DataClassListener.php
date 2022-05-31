<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

/**
 * DataClassListener which can be extended to listen to the crud functionality of a dataclass
 *
 * @package Chamilo\Libraries\Storage\DataClass\Listeners
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataClassListener
{
    const AFTER_CREATE = 'onAfterCreate';
    const AFTER_DELETE = 'onAfterDelete';
    const AFTER_SET_PROPERTY = 'onAfterSetProperty';
    const AFTER_UPDATE = 'onAfterUpdate';
    const BEFORE_CREATE = 'onBeforeCreate';
    const BEFORE_DELETE = 'onBeforeDelete';
    const BEFORE_SET_PROPERTY = 'onBeforeSetProperty';
    const BEFORE_UPDATE = 'onBeforeUpdate';
    const GET_DEPENDENCIES = 'onGetDependencies';

    public function onAfterCreate(bool $success): bool
    {
        return true;
    }

    public function onAfterDelete(bool $success): bool
    {
        return true;
    }

    public function onAfterSetProperty(string $name, string $value): bool
    {
        return true;
    }

    public function onAfterUpdate(bool $success): bool
    {
        return true;
    }

    public function onBeforeCreate(): bool
    {
        return true;
    }

    public function onBeforeDelete(): bool
    {
        return true;
    }

    public function onBeforeSetProperty(string $name, string $value): bool
    {
        return true;
    }

    public function onBeforeUpdate(): bool
    {
        return true;
    }

    /**
     * @param string[] $dependencies
     */
    public function onGetDependencies(array &$dependencies = []): bool
    {
        return true;
    }
}
