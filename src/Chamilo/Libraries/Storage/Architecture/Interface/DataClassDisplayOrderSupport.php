<?php
namespace Chamilo\Libraries\Storage\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Storage\DataClass\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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

    public function setDefaultProperty(string $name, mixed $value);
}