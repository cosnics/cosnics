<?php

namespace Chamilo\Application\Lti\Domain\Exception;

/**
 * Class ParseMessageException
 * @package Chamilo\Application\Lti\Domain\Exception
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ParseMessageException extends \RuntimeException
{
    /**
     * ParseMessageException constructor.
     *
     * @param string $errorMessage
     */
    public function __construct(string $errorMessage = null)
    {
        parent::__construct(sprintf('Could not parse the XML message: %s', $errorMessage));
    }
}