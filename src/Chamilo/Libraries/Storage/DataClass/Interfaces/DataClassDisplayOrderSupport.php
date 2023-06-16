<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * Interface DisplayOrderDataClassSupport
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 */
interface DataClassDisplayOrderSupport
{

    /**
     * @return string[]
     */
    public function getDefaultProperties(): array;

    public function getDefaultProperty(string $name): mixed;

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames(): array;

    public function getDisplayOrderPropertyName(): string;

    public function getId(): ?string;

    public function isIdentified(): bool;

    /**
     * @param mixed $value
     */
    public function setDefaultProperty(string $name, mixed $value);
}