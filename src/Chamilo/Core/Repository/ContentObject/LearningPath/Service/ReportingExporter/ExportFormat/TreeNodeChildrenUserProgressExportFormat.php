<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat;

use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Export format for the progress of all the children of a given treenode for a given user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeChildrenUserProgressExportFormat implements ExportFormatInterface
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
     * @var bool
     */
    protected $completed;

    /**
     * @var int
     */
    protected $averageScore;

    /**
     * @var int
     */
    protected $maximumScore;

    /**
     * @var int
     */
    protected $minimumScore;

    /**
     * @var int
     */
    protected $lastScore;

    /**
     * @var string
     */
    protected $time;

    /**
     * TreeNodeChildrenUserProgressExportFormat constructor.
     *
     * @param string $type
     * @param string $title
     * @param bool $completed
     * @param int $averageScore
     * @param int $maximumScore
     * @param int $minimumScore
     * @param int $lastScore
     * @param string $time
     */
    public function __construct(
        $type, $title, $completed, $averageScore, $maximumScore, $minimumScore, $lastScore, $time
    )
    {
        $this->type = $type;
        $this->title = $title;
        $this->completed = $completed;
        $this->averageScore = $averageScore;
        $this->maximumScore = $maximumScore;
        $this->minimumScore = $minimumScore;
        $this->lastScore = $lastScore;
        $this->time = $time;
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
            'completed' => (string) $this->completed,
            'average_score' => $this->averageScore . '%',
            'minimum_score' => $this->minimumScore . '%',
            'maximum_score' => $this->maximumScore . '%',
            'last_score' => $this->lastScore . '%',
            'time' => DatetimeUtilities::format_seconds_to_hours($this->time),
        ];
    }
}
