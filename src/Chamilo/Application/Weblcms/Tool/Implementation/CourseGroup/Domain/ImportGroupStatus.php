<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer\ImportedGroup;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ImportGroupStatus
{
    const STATUS_CREATED = 1;
    const STATUS_FAILED = 2;
    const STATUS_SKIPPING = 3;

    protected string $status;
    protected ?string $message;
    protected ImportedGroup $importedGroup;

    public function __construct(ImportedGroup $importedGroup, string $status, ?string $message = '')
    {
        $this->status = $status;
        $this->message = $message;
        $this->importedGroup = $importedGroup;
    }

    /**
     * @return ImportedGroup
     */
    public function getImportedGroup(): ImportedGroup
    {
        return $this->importedGroup;
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
