<?php

namespace Chamilo\Application\Lti\Domain\Exception;

/**
 * @package Chamilo\Application\Lti\Domain\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UnsupportedActionException extends \RuntimeException
{
    /**
     * ParseMessageException constructor.
     *
     * @param string $action
     */
    public function __construct(string $action = null)
    {
        parent::__construct(sprintf('The action %s is not supported', $action));
    }
}