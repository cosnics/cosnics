<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface DataClassExtensionInterface
{
    public const PROPERTIES_ADDITIONAL = 'additional_properties';

    public function checkForAdditionalProperties(): static;

    public function getAdditionalProperties(): array;

    public function getAdditionalProperty(string $name): mixed;

    public static function getAdditionalPropertyNames(): array;

    public static function isAdditionalPropertyName(string $name): bool;

    public function setAdditionalProperties(array $additionalProperties): static;

    public function setAdditionalProperty(string $name, mixed $value): static;
}