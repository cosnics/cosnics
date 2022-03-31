<?php

namespace Chamilo\Core\Notification\Domain;

/**
 * @package Chamilo\Core\Notification\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationDTO
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $time;

    /**
     * @var bool
     *
     * @SerializedName("isRead")
     */
    protected $isRead;

    /**
     * @var bool
     *
     * @SerializedName("isNew")
     */
    protected $isNew;

    /**
     * @var FilterDTO[]
     */
    protected $filters;

    /**
     * NotificationDTO constructor.
     *
     * @param int $id
     * @param string $message
     * @param string $time
     * @param bool $isRead
     * @param bool $isNew
     * @param \Chamilo\Core\Notification\Domain\FilterDTO[] $filters
     */
    public function __construct(
        int $id, string $message, string $time, bool $isRead, bool $isNew, array $filters = []
    )
    {
        $this->id = $id;
        $this->message = $message;
        $this->time = $time;
        $this->isRead = $isRead;
        $this->isNew = $isNew;
        $this->filters = $filters;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * @return \Chamilo\Core\Notification\Domain\FilterDTO[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

}