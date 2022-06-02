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
    public const AFTER_CREATE = 'onAfterCreate';
    public const AFTER_DELETE = 'onAfterDelete';
    public const AFTER_SET_PROPERTY = 'onAfterSetProperty';
    public const AFTER_UPDATE = 'onAfterUpdate';
    public const BEFORE_CREATE = 'onBeforeCreate';
    public const BEFORE_DELETE = 'onBeforeDelete';
    public const BEFORE_SET_PROPERTY = 'onBeforeSetProperty';
    public const BEFORE_UPDATE = 'onBeforeUpdate';
    public const GET_DEPENDENCIES = 'onGetDependencies';

    abstract public function onAfterCreate(bool $success): bool;

    abstract public function onAfterDelete(bool $success): bool;

    abstract public function onAfterSetProperty(string $name, string $value): bool;

    abstract public function onAfterUpdate(bool $success): bool;

    abstract public function onBeforeCreate(): bool;

    abstract public function onBeforeDelete(): bool;

    abstract public function onBeforeSetProperty(string $name, string $value): bool;

    abstract public function onBeforeUpdate(): bool;

    /**
     * @param string[] $dependencies
     */
    abstract public function onGetDependencies(array &$dependencies = []): bool;
}
