<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface CompositeDataClassInterface
{
    public const CONTEXT = StringUtilities::LIBRARIES;
    public const PROPERTIES_ADDITIONAL = 'additional_properties';
    public const PROPERTY_TYPE = 'type';

    public function checkForAdditionalProperties(): static;

    /**
     * @return string[] An associative array containing the properties.
     */
    public function getAdditionalProperties(): array;

    public function getAdditionalProperty(string $name): mixed;

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array;

    public static function getCompositeDataClassName(): string;

    public function getType(): string;

    public static function hasAdditionalPropertyNames(): bool;

    public static function isAdditionalPropertyName(string $name): bool;

    /**
     * @param string[] $additionalProperties
     */
    public function setAdditionalProperties(array $additionalProperties): static;

    /**
     * @param mixed $value The new value for the property.
     */
    public function setAdditionalProperty(string $name, mixed $value): static;

    public function setType(string $type): static;
}
