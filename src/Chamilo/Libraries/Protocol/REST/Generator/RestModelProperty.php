<?php

namespace Chamilo\Libraries\Protocol\REST\Generator;

use Hogent\Libraries\Architecture\Exception\ValueNotInArrayException;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RestModelProperty
{
    const AVAILABLE_TYPES = ["string", "int", "bool", "array", "object"];

    protected string $name;
    protected string $type;
    protected bool $isArray;
    protected bool $isNullable;
    protected $value;

    public function __construct(string $name, string $type, bool $isArray, bool $isNullable, $value)
    {
        $this->name = $name;

        if(!in_array($type, self::AVAILABLE_TYPES))
        {
            throw new ValueNotInArrayException("type", $type, self::AVAILABLE_TYPES);
        }

        $this->type = $isNullable ? '?' . $type : $type;
        $this->isArray = $isArray;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isArray()
    {
        return $this->isArray;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

}