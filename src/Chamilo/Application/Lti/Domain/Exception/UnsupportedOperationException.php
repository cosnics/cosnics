<?php

namespace Chamilo\Application\Lti\Domain\Exception;

/**
 * @package Chamilo\Application\Lti\Domain\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UnsupportedOperationException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $operation;

    /**
     * ParseMessageException constructor.
     *
     * @param string $operation
     */
    public function __construct(string $operation = null)
    {
        parent::__construct(sprintf('The operation %s is not supported', $operation));
        $this->operation = $operation;
    }

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }
}