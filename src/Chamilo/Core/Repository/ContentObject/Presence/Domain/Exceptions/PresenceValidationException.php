<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Domain\Exceptions;

/**
 * Class PresenceValidationException
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
     * @var array|null
     */
    protected ?array $savedStatus;

    /**
     * PresenceValidationException constructor.
     * @param string $code
     * @param int $presenceStatusId
     */
    public function __construct(string $code, int $presenceStatusId, ?array $savedStatus = null)
    {
        parent::__construct(sprintf('%s %s', $code, $presenceStatusId));

        $this->code = $code;
        $this->presenceStatusId = $presenceStatusId;
        $this->savedStatus = $savedStatus;
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

    /**
     * @return array|null
     */
    public function getSavedStatus(): ?array
    {
        return $this->savedStatus;
    }
}
