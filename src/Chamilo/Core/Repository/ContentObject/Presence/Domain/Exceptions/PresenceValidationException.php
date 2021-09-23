<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Domain\Exceptions;

/**
 * Class DuplicateFieldsException
 * @package Chamilo\Core\Repository\ContentObject\Presence\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class PresenceValidationException extends \Exception
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var int
     */
    protected int $presenceStatusId;

    /**
     * PresenceValidationException constructor.
     * @param string $code
     * @param int $presenceStatusId
     */
    public function __construct(string $code, int $presenceStatusId)
    {
        parent::__construct(sprintf('%s: id %s', $code, $presenceStatusId));

        $this->code = $code;
        $this->presenceStatusId = $presenceStatusId;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getPresenceStatusId(): int
    {
        return $this->presenceStatusId;
    }
}
