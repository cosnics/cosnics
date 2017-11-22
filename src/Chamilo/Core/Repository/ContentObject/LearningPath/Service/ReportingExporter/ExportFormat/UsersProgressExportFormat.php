<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat;

/**
 * Export format for the progress of users
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UsersProgressExportFormat implements ExportFormatInterface
{
    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $progress;

    /**
     * @var boolean
     */
    protected $completed;

    /**
     * @var boolean
     */
    protected $started;

    /**
     * UsersProgressExportFormat constructor.
     *
     * @param string $lastName
     * @param string $firstName
     * @param string $email
     * @param int $progress
     * @param bool $completed
     * @param bool $started
     */
    public function __construct($lastName, $firstName, $email, $progress = 0, $completed = true, $started = true)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->progress = $progress;
        $this->completed = $completed;
        $this->started = $started;
    }

    /**
     * Converts the export format to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'email' => $this->email,
            'progress' => $this->progress,
            'completed' => (int) $this->completed,
            'started' => (int) $this->started
        ];
    }
}