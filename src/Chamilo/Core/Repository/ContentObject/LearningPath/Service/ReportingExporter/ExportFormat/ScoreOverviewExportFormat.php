<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat;

/**
 * Class ScoreOverviewExportFormat
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ScoreOverviewExportFormat implements  ExportFormatInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $numberOfAttempts;

    /**
     * @var string
     */
    protected $minimumScore;

    /**
     * @var string
     */
    protected $maximumScore;

    /**
     * @var string
     */
    protected $averageScore;

    /**
     * @var string
     */
    protected $lastScore;

    /**
     * ScoreOverviewExportFormat constructor.
     *
     * @param string $type
     * @param string $title
     * @param string $path
     * @param int $numberOfAttempts
     * @param string $minimumScore
     * @param string $maximumScore
     * @param string $averageScore
     * @param string $lastScore
     */
    public function __construct(
        string $type, string $title, string $path, int $numberOfAttempts, string $minimumScore, string $maximumScore,
        string $averageScore, string $lastScore
    )
    {
        $this->type = $type;
        $this->title = $title;
        $this->path = $path;
        $this->numberOfAttempts = $numberOfAttempts;
        $this->minimumScore = $minimumScore;
        $this->maximumScore = $maximumScore;
        $this->averageScore = $averageScore;
        $this->lastScore = $lastScore;
    }

    /**
     * Converts the export format to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'path' => trim($this->path),
            'number_of_attempts' => $this->numberOfAttempts,
            'average_score' => $this->averageScore . '%',
            'minimum_score' => $this->minimumScore . '%',
            'maximum_score' => $this->maximumScore . '%',
            'last_score' => $this->lastScore . '%'
        ];
    }
}