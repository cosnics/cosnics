<?php

namespace Chamilo\Libraries\Architecture\Exceptions;

/**
 * @package Hogent\Integration\Panopto\Domain\Exception
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ValueNotInArrayException extends \InvalidArgumentException
{
    /**
     * @param string $argumentName
     * @param mixed $value
     * @param array $allowedValues
     */
    public function __construct(string $argumentName, $value, array $allowedValues)
    {
        parent::__construct(
            sprintf(
                'The given argument %s with value %s is not in the list of allowed values (%s)', $argumentName, $value,
                implode(', ', $allowedValues)
            )
        );
    }
}
