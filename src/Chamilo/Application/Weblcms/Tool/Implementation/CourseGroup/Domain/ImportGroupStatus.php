<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ImportGroupStatus
{
    const STATUS_CREATED = 1;
    const STATUS_FAILED = 2;
    const STATUS_SKIPPING = 3;

    protected string $groupName;
    protected string $status;
    protected ?string $message;

    public function __construct(string $groupName, string $status, ?string $message = '')
    {
        $this->groupName = $groupName;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
