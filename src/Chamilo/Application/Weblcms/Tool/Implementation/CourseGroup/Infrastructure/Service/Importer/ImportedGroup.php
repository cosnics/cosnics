<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ImportedGroup
{
    protected string $title;
    protected string $description;
    protected int $maximumMembers;
    protected bool $selfRegistrationAllowed;
    protected bool $selfUnregistrationAllowed;

    public function __construct(
        string $title, string $description = '', int $maximumMembers = 0, bool $selfRegistrationAllowed = false,
        bool $selfUnregistrationAllowed = false
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->maximumMembers = $maximumMembers;
        $this->selfRegistrationAllowed = $selfRegistrationAllowed;
        $this->selfUnregistrationAllowed = $selfUnregistrationAllowed;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getMaximumMembers(): int
    {
        return $this->maximumMembers;
    }

    /**
     * @return bool
     */
    public function isSelfRegistrationAllowed(): bool
    {
        return $this->selfRegistrationAllowed;
    }

    /**
     * @return bool
     */
    public function isSelfUnregistrationAllowed(): bool
    {
        return $this->selfUnregistrationAllowed;
    }
}
