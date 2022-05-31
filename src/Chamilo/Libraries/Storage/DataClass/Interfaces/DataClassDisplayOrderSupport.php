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

    /**
     * @return mixed
     */
    public function getDefaultProperty(string $name);

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames(): array;

    public function getDisplayOrderPropertyName(): string;

    public function getId(): ?int;

    public function isIdentified(): bool;

    /**
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function setDefaultProperty(string $name, $value);
}